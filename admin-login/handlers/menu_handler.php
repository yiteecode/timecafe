<?php
session_start();
require_once '../../db-config.php';

// Check authentication
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Handle different actions
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'add':
        handleAddItem();
        break;
    case 'edit':
        handleEditItem();
        break;
    case 'delete':
        handleDeleteItem();
        break;
    case 'get':
        handleGetItem();
        break;
    case 'update_order':
        handleUpdateOrder();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

function handleAddItem() {
    global $connect;
    
    // Validate required fields
    if (empty($_POST['name']) || empty($_POST['category_id']) || empty($_POST['price'])) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
        return;
    }

    // Handle image upload
    $image_name = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $image_name = handleImageUpload($_FILES['image']);
        if (!$image_name) {
            echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
            return;
        }
    }

    // Get the next sort order
    $sort_query = "SELECT MAX(sort_order) as max_order FROM menu_items WHERE category_id = ?";
    $stmt = $connect->prepare($sort_query);
    $stmt->bind_param('i', $_POST['category_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $next_order = ($result->fetch_assoc()['max_order'] ?? 0) + 1;

    // Insert menu item
    $query = "INSERT INTO menu_items (name, category_id, description, price, image, sort_order, active) 
              VALUES (?, ?, ?, ?, ?, ?, 1)";
            
            $stmt = $connect->prepare($query);
    $stmt->bind_param('sisdsi', 
        $_POST['name'],
        $_POST['category_id'],
        $_POST['description'],
        $_POST['price'],
        $image_name,
        $next_order
    );

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Menu item added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add menu item']);
    }
}

function handleEditItem() {
    global $connect;
    
    if (empty($_POST['id']) || empty($_POST['name']) || empty($_POST['category_id']) || empty($_POST['price'])) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
        return;
    }

    $image_name = $_POST['old_image'] ?? '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $new_image = handleImageUpload($_FILES['image']);
        if ($new_image) {
            // Delete old image if exists
            if (!empty($image_name)) {
                deleteImage($image_name);
            }
            $image_name = $new_image;
        }
    }

    $query = "UPDATE menu_items SET 
              name = ?, 
              category_id = ?, 
              description = ?, 
              price = ?, 
              image = ?
              WHERE id = ?";
    
    $stmt = $connect->prepare($query);
    $stmt->bind_param('sisdsi',
        $_POST['name'],
        $_POST['category_id'],
        $_POST['description'],
        $_POST['price'],
        $image_name,
        $_POST['id']
    );

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Menu item updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update menu item']);
    }
}

function handleDeleteItem() {
    global $connect;
    
    if (empty($_POST['id'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid item ID']);
        return;
    }

    // Get the image name before deleting
    $query = "SELECT image FROM menu_items WHERE id = ?";
    $stmt = $connect->prepare($query);
    $stmt->bind_param('i', $_POST['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();

    // Soft delete the item
    $query = "UPDATE menu_items SET active = 0 WHERE id = ?";
    $stmt = $connect->prepare($query);
    $stmt->bind_param('i', $_POST['id']);

    if ($stmt->execute()) {
        // Delete the image file if it exists
        if (!empty($item['image'])) {
            deleteImage($item['image']);
        }
        echo json_encode(['success' => true, 'message' => 'Menu item deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete menu item']);
    }
}

function handleGetItem() {
    global $connect;
    
    if (empty($_GET['id'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid item ID']);
        return;
    }

    $query = "SELECT * FROM menu_items WHERE id = ?";
    $stmt = $connect->prepare($query);
    $stmt->bind_param('i', $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();

    if ($item) {
        echo json_encode(['success' => true, 'item' => $item]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Item not found']);
    }
}

function handleUpdateOrder() {
    global $connect;
    
    $data = json_decode(file_get_contents('php://input'), true);
    if (empty($data['items'])) {
        echo json_encode(['success' => false, 'message' => 'No items to update']);
        return;
    }

    $success = true;
    foreach ($data['items'] as $item) {
        $query = "UPDATE menu_items SET sort_order = ? WHERE id = ?";
        $stmt = $connect->prepare($query);
        $stmt->bind_param('ii', $item['order'], $item['id']);
        if (!$stmt->execute()) {
            $success = false;
            break;
        }
    }

    echo json_encode(['success' => $success]);
}

function handleImageUpload($file) {
    $upload_dir = '../../uploads/menu/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_types = ['jpg', 'jpeg', 'png', 'webp'];
    
    if (!in_array($file_extension, $allowed_types)) {
        return false;
    }

    $max_size = 2 * 1024 * 1024; // 2MB
    if ($file['size'] > $max_size) {
        return false;
    }

    $new_filename = uniqid() . '.' . $file_extension;
    $destination = $upload_dir . $new_filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $new_filename;
    }

    return false;
}

function deleteImage($filename) {
    $file_path = '../../uploads/menu/' . $filename;
    if (file_exists($file_path)) {
        unlink($file_path);
    }
}

mysqli_close($connect);
?> 