<?php
session_start();
require_once '../../db-config.php';

// Debug log
error_log('Gallery handler request received');

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
            if (empty($_FILES['image'])) {
                throw new Exception('Image is required');
            }
            
            // Handle image upload
            $image = $_FILES['image'];
            $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
            
            if (!in_array($image['type'], $allowed_types)) {
                throw new Exception('Invalid image format. Only JPG, PNG and WEBP are allowed');
            }
            
            $filename = uniqid() . '_' . basename($image['name']);
            $upload_path = '../../uploads/gallery/' . $filename;
            
            if (!move_uploaded_file($image['tmp_name'], $upload_path)) {
                throw new Exception('Failed to upload image');
            }
            
            // Insert into database
            $stmt = $connect->prepare("INSERT INTO gallery (title, description, image) VALUES (?, ?, ?)");
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $stmt->bind_param("sss", $title, $description, $filename);
            
            if (!$stmt->execute()) {
                // Delete uploaded image if database insert fails
                unlink($upload_path);
                throw new Exception('Failed to add gallery item: ' . $stmt->error);
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Gallery item added successfully'
            ]);
            break;

        case 'edit':
            if (empty($_POST['id'])) {
                throw new Exception('Gallery item ID is required');
            }
            
            $id = intval($_POST['id']);
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            
            // Start with current image
            $stmt = $connect->prepare("SELECT image FROM gallery WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $current_item = $result->fetch_assoc();
            $image_name = $current_item['image'];

            // Handle new image if uploaded
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['image'];
                $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
                
                if (!in_array($file['type'], $allowed_types)) {
                    throw new Exception('Invalid image format. Only JPG, PNG and WEBP are allowed');
                }
                
                // Generate new filename
                $new_image_name = uniqid() . '_' . basename($file['name']);
                $upload_path = '../../uploads/gallery/' . $new_image_name;
                
                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    // Delete old image if exists
                    if ($image_name && file_exists('../../uploads/gallery/' . $image_name)) {
                        unlink('../../uploads/gallery/' . $image_name);
                    }
                    $image_name = $new_image_name;
                } else {
                    throw new Exception('Failed to upload new image');
                }
            }

            // Update database
            $stmt = $connect->prepare("UPDATE gallery SET title = ?, description = ?, image = ? WHERE id = ?");
            $stmt->bind_param("sssi", $title, $description, $image_name, $id);
            
            if (!$stmt->execute()) {
                // If we uploaded a new image but database update failed, clean it up
                if (isset($new_image_name) && file_exists('../../uploads/gallery/' . $new_image_name)) {
                    unlink('../../uploads/gallery/' . $new_image_name);
                }
                throw new Exception('Failed to update gallery item: ' . $stmt->error);
            }

            echo json_encode([
                'success' => true,
                'message' => 'Gallery item updated successfully'
            ]);
            break;

        case 'delete':
            if (empty($_POST['id'])) {
                throw new Exception('Gallery item ID is required');
            }
            
            $id = intval($_POST['id']);
            
            // Get image filename before deletion
            $stmt = $connect->prepare("SELECT image FROM gallery WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $item = $result->fetch_assoc();
            
            // Delete from database
            $stmt = $connect->prepare("DELETE FROM gallery WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to delete gallery item: ' . $stmt->error);
            }
            
            // Delete image file if exists
            if ($item && $item['image']) {
                $image_path = '../../uploads/gallery/' . $item['image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Gallery item deleted successfully'
            ]);
            break;

        case 'get':
            if (empty($_GET['id'])) {
                throw new Exception('Gallery item ID is required');
            }
            
            $id = intval($_GET['id']);
            $stmt = $connect->prepare("SELECT * FROM gallery WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $item = $result->fetch_assoc();
            
            if (!$item) {
                throw new Exception('Gallery item not found');
            }
            
            echo json_encode([
                'success' => true,
                'item' => $item
            ]);
            break;

        default:
            throw new Exception('Invalid action');
    }

} catch (Exception $e) {
    error_log("Gallery handler error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

mysqli_close($connect);
?> 