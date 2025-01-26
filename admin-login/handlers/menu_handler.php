<?php
session_start();
require_once '../../db-config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

// Create uploads directory if it doesn't exist
$upload_dir = '../../uploads/menu/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

header('Content-Type: application/json');

function handleResponse($success, $message, $data = null) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

function validateImage($file) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Error uploading file');
    }
    
    if (!in_array($file['type'], $allowed_types)) {
        throw new Exception('Invalid file type. Please use JPG, PNG, or WEBP');
    }
    
    if ($file['size'] > $max_size) {
        throw new Exception('Image size must be less than 5MB');
    }
    
    return true;
}

try {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            // Validate required fields
            $name = trim($_POST['name'] ?? '');
            $price = floatval($_POST['price'] ?? 0);
            $category_id = intval($_POST['category_id'] ?? 0);
            $description = trim($_POST['description'] ?? '');
            
            if (empty($name)) {
                handleResponse(false, 'Name is required');
            }
            
            if ($price <= 0) {
                handleResponse(false, 'Price must be greater than 0');
            }
            
            if ($category_id <= 0) {
                handleResponse(false, 'Category is required');
            }
            
            // Handle image upload
            $image_filename = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                validateImage($_FILES['image']);
                $image_filename = uniqid() . '_' . basename($_FILES['image']['name']);
                $upload_path = $upload_dir . $image_filename;
                
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    handleResponse(false, 'Failed to upload image');
                }
            }
            
            // Insert into database
            $stmt = $connect->prepare("
                INSERT INTO menu_items (name, price, category_id, description, image, active) 
                VALUES (?, ?, ?, ?, ?, 1)
            ");
            
            if (!$stmt) {
                handleResponse(false, 'Database error: ' . $connect->error);
            }
            
            $stmt->bind_param('sdiss', $name, $price, $category_id, $description, $image_filename);
            
            if (!$stmt->execute()) {
                handleResponse(false, 'Failed to add menu item: ' . $stmt->error);
            }
            
            handleResponse(true, 'Menu item added successfully', [
                'id' => $stmt->insert_id,
                'name' => $name,
                'price' => $price,
                'category_id' => $category_id,
                'description' => $description,
                'image' => $image_filename
            ]);
            break;
            
        case 'edit':
            // Implementation for edit action
            break;
            
        case 'delete':
            $id = intval($_POST['id'] ?? 0);
            if ($id <= 0) {
                handleResponse(false, 'Invalid menu item ID');
            }
            
            // Get current image filename
            $stmt = $connect->prepare("SELECT image FROM menu_items WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $item = $result->fetch_assoc();
            
            // Delete the image file if it exists
            if ($item && $item['image']) {
                $image_path = $upload_dir . $item['image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            
            // Delete from database
            $stmt = $connect->prepare("DELETE FROM menu_items WHERE id = ?");
            $stmt->bind_param('i', $id);
            
            if (!$stmt->execute()) {
                handleResponse(false, 'Failed to delete menu item');
            }
            
            handleResponse(true, 'Menu item deleted successfully');
            break;
            
        default:
            handleResponse(false, 'Invalid action specified');
    }
    
} catch (Exception $e) {
    handleResponse(false, $e->getMessage());
}

mysqli_close($connect);
?> 