<?php
session_start();
require_once '../../db-config.php';

// Check admin authentication
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

// Create uploads directory if it doesn't exist
$upload_dir = '../../uploads/about/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

function handleAboutUpdate() {
    global $connect, $upload_dir;
    $response = ['success' => false, 'message' => ''];
    
    try {
        $heading = trim($_POST['heading'] ?? '');
        $subheading = trim($_POST['subheading'] ?? '');
        $main_content = trim($_POST['main_content'] ?? '');
        $mission = trim($_POST['mission'] ?? '');
        $vision = trim($_POST['vision'] ?? '');
        $video_url = trim($_POST['video_url'] ?? '');
        
        if (empty($heading) || empty($main_content)) {
            throw new Exception('Heading and main content are required');
        }

        // Start transaction
        $connect->begin_transaction();

        // Handle image upload if provided
        $image_sql = '';
        $params = [$heading, $subheading, $main_content, $mission, $vision, $video_url];
        $types = "ssssss";

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['image'];
            $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
            $max_size = 5 * 1024 * 1024;
            
            if (!in_array($file['type'], $allowed_types)) {
                throw new Exception('Invalid file type');
            }
            
            if ($file['size'] > $max_size) {
                throw new Exception('File too large');
            }
            
            $filename = 'about_' . uniqid() . '_' . basename($file['name']);
            $upload_path = $upload_dir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                $image_sql = ", image = ?";
                $params[] = $filename;
                $types .= "s";
                
                // Delete old image if exists
                $stmt = $connect->prepare("SELECT image FROM about_section WHERE id = 1");
                $stmt->execute();
                $old_image = $stmt->get_result()->fetch_assoc()['image'] ?? null;
                
                if ($old_image && file_exists($upload_dir . $old_image)) {
                    unlink($upload_dir . $old_image);
                }
            }
        }

        // Update or insert about section
        $sql = "INSERT INTO about_section 
                (id, heading, subheading, main_content, mission, vision, video_url) 
                VALUES (1, ?, ?, ?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE 
                heading = VALUES(heading),
                subheading = VALUES(subheading),
                main_content = VALUES(main_content),
                mission = VALUES(mission),
                vision = VALUES(vision),
                video_url = VALUES(video_url)";

        if ($image_sql) {
            $sql = "INSERT INTO about_section 
                    (id, heading, subheading, main_content, mission, vision, video_url, image) 
                    VALUES (1, ?, ?, ?, ?, ?, ?, ?) 
                    ON DUPLICATE KEY UPDATE 
                    heading = VALUES(heading),
                    subheading = VALUES(subheading),
                    main_content = VALUES(main_content),
                    mission = VALUES(mission),
                    vision = VALUES(vision),
                    video_url = VALUES(video_url),
                    image = VALUES(image)";
        }

        $stmt = $connect->prepare($sql);
        $stmt->bind_param($types, ...$params);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to update about section');
        }

        $connect->commit();
        
        $response['success'] = true;
        $response['message'] = 'About section updated successfully';
        
    } catch (Exception $e) {
        $connect->rollback();
        $response['message'] = $e->getMessage();
    }
    
    return $response;
}

function handleFeatureUpdate() {
    global $connect;
    $response = ['success' => false, 'message' => ''];
    
    try {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $icon = trim($_POST['icon'] ?? '');
        
        if (empty($title)) {
            throw new Exception('Title is required');
        }

        if ($id) {
            // Update existing feature
            $stmt = $connect->prepare("
                UPDATE about_features 
                SET title = ?, description = ?, icon = ?
                WHERE id = ?
            ");
            $stmt->bind_param("sssi", $title, $description, $icon, $id);
        } else {
            // Add new feature
            $stmt = $connect->prepare("
                INSERT INTO about_features (title, description, icon) 
                VALUES (?, ?, ?)
            ");
            $stmt->bind_param("sss", $title, $description, $icon);
        }
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to save feature');
        }

        $response['success'] = true;
        $response['message'] = $id ? 'Feature updated successfully' : 'Feature added successfully';
        
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
    
    return $response;
}

function deleteFeature() {
    global $connect;
    $response = ['success' => false, 'message' => ''];
    
    try {
        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        if (!$id) {
            throw new Exception('Invalid feature ID');
        }

        $stmt = $connect->prepare("DELETE FROM about_features WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to delete feature');
        }

        $response['success'] = true;
        $response['message'] = 'Feature deleted successfully';
        
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
    
    return $response;
}

// Handle AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $action = $_POST['about_action'] ?? '';
    
    switch ($action) {
        case 'update_main':
            echo json_encode(handleAboutUpdate());
            break;
        case 'update_feature':
            echo json_encode(handleFeatureUpdate());
            break;
        case 'delete_feature':
            echo json_encode(deleteFeature());
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