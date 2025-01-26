<?php
session_start();
require_once '../../db-config.php';

// Debug log
error_log('Chef handler request received');

// Check admin authentication
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

// Create uploads directory if it doesn't exist
$upload_dir = '../../uploads/chefs/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

function handleChefUpload($connect) {
    global $upload_dir;
    error_log('Processing chef upload');
    
    try {
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('No image file uploaded or upload error occurred');
        }

        $file = $_FILES['image'];
        $name = trim($_POST['name'] ?? '');
        $profession = trim($_POST['profession'] ?? '');
        $description = trim($_POST['description'] ?? '');

        // Validate inputs
        if (empty($name)) {
            throw new Exception('Name is required');
        }
        if (empty($profession)) {
            throw new Exception('Profession is required');
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
        $filename = uniqid('chef_') . '.' . $extension;
        $filepath = $upload_dir . $filename;

        // Start transaction
        $connect->begin_transaction();

        // Get the next sort order
        $stmt = $connect->prepare("SELECT COALESCE(MAX(sort_order), 0) + 1 AS next_sort FROM chefs");
        $stmt->execute();
        $result = $stmt->get_result();
        $next_sort = $result->fetch_assoc()['next_sort'];

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception('Failed to move uploaded file');
        }

        // Insert into database with the schema fields
        $stmt = $connect->prepare("
            INSERT INTO chefs (name, profession, description, image, active, sort_order) 
            VALUES (?, ?, ?, ?, true, ?)
        ");

        if (!$stmt->bind_param('ssssi', $name, $profession, $description, $filename, $next_sort)) {
            throw new Exception('Failed to bind parameters');
        }

        if (!$stmt->execute()) {
            throw new Exception('Failed to save to database');
        }

        $connect->commit();
        
        error_log('Chef added successfully: ' . $name);

        return [
            'success' => true,
            'message' => 'Chef added successfully',
            'data' => [
                'id' => $connect->insert_id,
                'name' => $name,
                'profession' => $profession,
                'description' => $description,
                'image' => $filename,
                'active' => true,
                'sort_order' => $next_sort,
                'created_at' => date('Y-m-d H:i:s')
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

function deleteChef() {
    global $connect, $upload_dir;
    $response = ['success' => false, 'message' => ''];
    
    try {
        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        if (!$id) {
            throw new Exception('Invalid chef ID');
        }

        try {
            // Begin transaction
            $connect->begin_transaction();
            
            // Get image filename first
            $stmt = $connect->prepare("SELECT image FROM chefs WHERE id = ?");
            if (!$stmt) {
                throw new Exception('Database prepare error: ' . $connect->error);
            }

            $stmt->bind_param("i", $id);
            if (!$stmt->execute()) {
                throw new Exception('Database execute error: ' . $stmt->error);
            }

            $result = $stmt->get_result();
            $chef = $result->fetch_assoc();
            
            if (!$chef) {
                throw new Exception('Chef not found in database');
            }

            // Delete database record
            $stmt = $connect->prepare("DELETE FROM chefs WHERE id = ?");
            if (!$stmt) {
                throw new Exception('Database prepare error: ' . $connect->error);
            }

            $stmt->bind_param("i", $id);
            if (!$stmt->execute()) {
                throw new Exception('Database execute error: ' . $stmt->error);
            }

            // Delete file after successful database deletion
            if ($chef['image']) {
                $filepath = $upload_dir . $chef['image'];
                if (file_exists($filepath) && !unlink($filepath)) {
                    throw new Exception('Failed to delete image file');
                }
            }

            // Commit transaction
            $connect->commit();
            
            $response['success'] = true;
            $response['message'] = 'Chef deleted successfully!';

        } catch (Exception $e) {
            // Rollback transaction on error
            $connect->rollback();
            throw $e;
        }
        
    } catch (Exception $e) {
        $response['success'] = false;
        $response['message'] = $e->getMessage();
        error_log('Chef delete error: ' . $e->getMessage());
    }
    
    return $response;
}

function editChef() {
    global $connect, $upload_dir;
    $response = ['success' => false, 'message' => ''];
    
    try {
        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        $name = trim($_POST['name'] ?? '');
        $profession = trim($_POST['profession'] ?? '');
        $description = trim($_POST['description'] ?? '');
        
        if (!$id || empty($name) || empty($profession)) {
            throw new Exception('Required fields are missing');
        }

        // Start transaction
        $connect->begin_transaction();

        // Handle image upload if new image is provided
        $image_sql = '';
        $params = [$name, $profession, $description, $id];
        $types = "sssi";

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            // Validate and upload new image
            $file = $_FILES['image'];
            $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
            $max_size = 5 * 1024 * 1024;
            
            if (!in_array($file['type'], $allowed_types)) {
                throw new Exception('Invalid file type');
            }
            
            if ($file['size'] > $max_size) {
                throw new Exception('File too large');
            }
            
            $filename = 'chef_' . uniqid() . '_' . basename($file['name']);
            $upload_path = $upload_dir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                $image_sql = ", image = ?";
                $params[] = $filename;
                $types .= "s";
                
                // Delete old image
                $stmt = $connect->prepare("SELECT image FROM chefs WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $old_image = $stmt->get_result()->fetch_assoc()['image'];
                
                if ($old_image && file_exists($upload_dir . $old_image)) {
                    unlink($upload_dir . $old_image);
                }
            }
        }

        // Update database
        $sql = "UPDATE chefs SET name = ?, profession = ?, description = ? $image_sql WHERE id = ?";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param($types, ...$params);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to update chef');
        }

        $connect->commit();
        
        $response['success'] = true;
        $response['message'] = 'Chef updated successfully';
        
    } catch (Exception $e) {
        $connect->rollback();
        $response['message'] = $e->getMessage();
    }
    
    return $response;
}

// Handle AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $action = $_POST['chef_action'] ?? '';
    error_log('Chef action received: ' . $action);
    
    switch ($action) {
        case 'add':
            echo json_encode(handleChefUpload($connect));
            break;
        case 'edit':
            echo json_encode(editChef());
            break;
        case 'delete':
            echo json_encode(deleteChef());
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