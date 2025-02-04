<?php
session_start();
require_once '../../db-config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

try {
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    if (empty($action)) {
        throw new Exception('No action specified');
    }

    switch ($action) {
        case 'add':
            // Validate required fields
            if (empty($_POST['name']) || empty($_POST['price']) || empty($_POST['category_id'])) {
                throw new Exception('Name, price and category are required');
            }

            // Handle image upload if present
            $image_name = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
                $max_size = 2 * 1024 * 1024; // 2MB

                if (!in_array($_FILES['image']['type'], $allowed_types)) {
                    throw new Exception('Invalid image format. Only JPG, PNG and WEBP are allowed');
                }

                if ($_FILES['image']['size'] > $max_size) {
                    throw new Exception('Image size must be less than 2MB');
                }

                $upload_dir = '../../uploads/menu/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $image_name = uniqid() . '_' . basename($_FILES['image']['name']);
                $upload_path = $upload_dir . $image_name;

                if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    throw new Exception('Failed to upload image');
                }
            }

            // Insert into database
            $stmt = $connect->prepare("
                INSERT INTO menu_items (name, description, price, category_id, image) 
                VALUES (?, ?, ?, ?, ?)
            ");

            if (!$stmt) {
                throw new Exception('Prepare statement failed: ' . $connect->error);
            }

            $name = $_POST['name'];
            $description = $_POST['description'] ?? '';
            $price = floatval($_POST['price']);
            $category_id = intval($_POST['category_id']);

            $stmt->bind_param("ssdis", $name, $description, $price, $category_id, $image_name);

            if (!$stmt->execute()) {
                // If image was uploaded but insert failed, delete the uploaded image
                if ($image_name && file_exists($upload_dir . $image_name)) {
                    unlink($upload_dir . $image_name);
                }
                throw new Exception('Failed to add menu item: ' . $stmt->error);
            }

            $stmt->close();

            echo json_encode([
                'success' => true,
                'message' => 'Menu item added successfully'
            ]);
            break;

        case 'get':
            if (empty($_GET['id'])) {
                throw new Exception('Menu item ID is required');
            }

            $id = intval($_GET['id']);
            if ($id <= 0) {
                throw new Exception('Invalid menu item ID');
            }
            
            error_log("Fetching menu item with ID: " . $id);
            
            $stmt = $connect->prepare("SELECT id, name, description, price, category_id, image FROM menu_items WHERE id = ?");
            if (!$stmt) {
                throw new Exception('Prepare statement failed: ' . $connect->error);
            }
            
            $stmt->bind_param("i", $id);
            
            if (!$stmt->execute()) {
                throw new Exception('Execute failed: ' . $stmt->error);
            }
            
            $result = $stmt->get_result();
            $item = $result->fetch_assoc();
            
            if ($item) {
                echo json_encode([
                    'success' => true, 
                    'item' => $item
                ]);
            } else {
                throw new Exception('Menu item not found');
            }
            $stmt->close();
            break;

        case 'update':
            if (empty($_POST['id'])) {
                throw new Exception('Menu item ID is required');
            }

            $id = $_POST['id'];
            $name = trim($_POST['name']);
            $description = trim($_POST['description']);
            $price = floatval($_POST['price']);
            $category_id = intval($_POST['category_id']);

            // Validate inputs
            if (empty($name)) throw new Exception('Name is required');
            if ($price <= 0) throw new Exception('Price must be greater than zero');
            if ($category_id <= 0) throw new Exception('Category is required');

            // Handle image upload if provided
            $image_update = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
                if (!in_array($_FILES['image']['type'], $allowed_types)) {
                    throw new Exception('Invalid image type. Please use JPG, PNG, or WebP');
                }

                $image_filename = uniqid() . '_' . basename($_FILES['image']['name']);
                $upload_dir = '../../uploads/menu/';
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_filename)) {
                    // Get and delete old image
                    $stmt = $connect->prepare("SELECT image FROM menu_items WHERE id = ?");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $old_image = $stmt->get_result()->fetch_assoc()['image'];
                    
                    if ($old_image && file_exists($upload_dir . $old_image)) {
                        unlink($upload_dir . $old_image);
                    }
                    
                    $image_update = ", image = ?";
                } else {
                    throw new Exception('Failed to upload image');
                }
            }

            // Update database
            $query = "UPDATE menu_items SET 
                     name = ?, 
                     description = ?, 
                     price = ?, 
                     category_id = ?" . $image_update . " 
                     WHERE id = ? AND active = 1";
            
            $stmt = $connect->prepare($query);
            
            if ($image_update) {
                $stmt->bind_param("ssdisi", $name, $description, $price, $category_id, $image_filename, $id);
            } else {
                $stmt->bind_param("ssdii", $name, $description, $price, $category_id, $id);
            }

            if (!$stmt->execute()) {
                throw new Exception("Failed to update menu item: " . $stmt->error);
            }

            echo json_encode([
                'success' => true,
                'message' => 'Menu item updated successfully',
                'data' => [
                    'id' => $id,
                    'name' => $name,
                    'description' => $description,
                    'price' => $price,
                    'category_id' => $category_id,
                    'image' => $image_filename ?? null
                ]
            ]);
            break;

        case 'delete':
            if (empty($_POST['id'])) {
                throw new Exception('Menu item ID is required');
            }

            $id = intval($_POST['id']);
            
            // First get the image filename to delete
            $stmt = $connect->prepare("SELECT image FROM menu_items WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $item = $result->fetch_assoc();
            
            // Delete from database
            $stmt = $connect->prepare("DELETE FROM menu_items WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to delete menu item: ' . $stmt->error);
            }
            
            // If deletion was successful and there was an image, delete it
            if ($stmt->affected_rows > 0 && !empty($item['image'])) {
                $image_path = '../../uploads/menu/' . $item['image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            
            $stmt->close();
            
            echo json_encode([
                'success' => true,
                'message' => 'Menu item deleted successfully'
            ]);
            break;

        default:
            throw new Exception('Invalid action');
    }

} catch (Exception $e) {
    error_log("Menu handler error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

exit;
?> 