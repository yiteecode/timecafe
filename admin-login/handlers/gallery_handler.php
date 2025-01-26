<?php
session_start();
require_once '../../db-config.php';

// Debug log
error_log('Gallery handler request received');

// Check admin authentication
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

// Create uploads directory if it doesn't exist
$upload_dir = '../../uploads/gallery/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

function handleImageUpload($connect) {
    global $upload_dir;
    error_log('Processing image upload');
    
    try {
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('No image file uploaded or upload error occurred');
        }

        $file = $_FILES['image'];
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');

        // Validate inputs
        if (empty($title)) {
            throw new Exception('Title is required');
        }

        // Validate file
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowed_types)) {
            throw new Exception('Invalid file type. Allowed types: JPG, PNG, WEBP');
        }

        if ($file['size'] > $max_size) {
            throw new Exception('File too large. Maximum size: 5MB');
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('gallery_') . '.' . $extension;
        $filepath = $upload_dir . $filename;

        // Start transaction
        $connect->begin_transaction();

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception('Failed to move uploaded file');
        }

        // Insert into database
        $stmt = $connect->prepare("
            INSERT INTO gallery (image, title, description, created_at) 
            VALUES (?, ?, ?, NOW())
        ");

        if (!$stmt->bind_param('sss', $filename, $title, $description)) {
            throw new Exception('Failed to bind parameters');
        }

        if (!$stmt->execute()) {
            throw new Exception('Failed to save to database');
        }

        $connect->commit();
        
        error_log('Image uploaded successfully: ' . $filename);

        return [
            'success' => true,
            'message' => 'Image uploaded successfully',
            'data' => [
                'id' => $connect->insert_id,
                'image' => $filename,
                'title' => $title,
                'description' => $description
            ]
        ];

    } catch (Exception $e) {
        error_log('Upload error: ' . $e->getMessage());
        if (isset($connect) && $connect->ping()) {
            $connect->rollback();
        }
        if (isset($filepath) && file_exists($filepath)) {
            unlink($filepath);
        }
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function deleteImage() {
    global $connect, $upload_dir;
    $response = ['success' => false, 'message' => ''];
    
    try {
        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        if (!$id) {
            throw new Exception('Invalid image ID');
        }

        // Begin transaction
        $connect->begin_transaction();

        // Get image filename
        $stmt = $connect->prepare("SELECT image FROM gallery WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $image = $result->fetch_assoc();

        if (!$image) {
            throw new Exception('Image not found');
        }

        // Delete from database
        $stmt = $connect->prepare("DELETE FROM gallery WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to delete image record');
        }

        // Delete file
        $filepath = $upload_dir . $image['image'];
        if (file_exists($filepath) && !unlink($filepath)) {
            throw new Exception('Failed to delete image file');
        }

        $connect->commit();
        
        $response['success'] = true;
        $response['message'] = 'Image deleted successfully';
        
    } catch (Exception $e) {
        if (isset($connect) && $connect->ping()) {
            $connect->rollback();
        }
        $response['message'] = $e->getMessage();
    }
    
    return $response;
}

// Handle AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $action = $_POST['gallery_action'] ?? '';
    error_log('Gallery action received: ' . $action);
    
    switch ($action) {
        case 'upload':
            echo json_encode(handleImageUpload($connect));
            break;
        case 'delete':
            echo json_encode(deleteImage());
            break;
        default:
            echo json_encode([
                'success' => false, 
                'message' => 'Invalid action specified'
            ]);
    }
    exit;
}
?> 