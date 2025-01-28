<?php
session_start();
require_once '../../db-config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Set error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Create uploads directory if it doesn't exist
$upload_dir = '../../uploads/hero/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate required fields
        if (empty($_POST['heading'])) {
            throw new Exception('Heading is required');
        }

        $heading = trim($_POST['heading']);
        $subheading = trim($_POST['subheading'] ?? '');
        
        // Get existing hero data
        $stmt = $connect->prepare("SELECT hero_image, video_url FROM hero_section WHERE id = 1");
        $stmt->execute();
        $existing = $stmt->get_result()->fetch_assoc();

        // Initialize update data
        $update_data = [
            'heading' => $heading,
            'subheading' => $subheading,
            'hero_image' => $existing['hero_image'] ?? null,
            'video_url' => $existing['video_url'] ?? null
        ];

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
            
            if (!in_array($_FILES['image']['type'], $allowed_types)) {
                throw new Exception('Invalid image type. Please use JPG, PNG, or WebP');
            }

            $image_filename = uniqid() . '_' . basename($_FILES['image']['name']);
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_filename)) {
                // Delete old image if exists
                if (!empty($existing['hero_image'])) {
                    @unlink($upload_dir . $existing['hero_image']);
                }
                $update_data['hero_image'] = $image_filename;
            } else {
                throw new Exception('Failed to upload image');
            }
        }

        // Handle video upload
        if (isset($_FILES['video']) && $_FILES['video']['error'] === 0) {
            $allowed_types = ['video/mp4', 'video/webm'];
            
            if (!in_array($_FILES['video']['type'], $allowed_types)) {
                throw new Exception('Invalid video type. Please use MP4 or WebM');
            }

            $video_filename = uniqid() . '_' . basename($_FILES['video']['name']);
            
            if (move_uploaded_file($_FILES['video']['tmp_name'], $upload_dir . $video_filename)) {
                // Delete old video if exists
                if (!empty($existing['video_url'])) {
                    @unlink($upload_dir . $existing['video_url']);
                }
                $update_data['video_url'] = $video_filename;
            } else {
                throw new Exception('Failed to upload video');
            }
        }

        // Update database
        $sql = "INSERT INTO hero_section (id, heading, subheading, hero_image, video_url) 
                VALUES (1, ?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE 
                heading = VALUES(heading),
                subheading = VALUES(subheading),
                hero_image = VALUES(hero_image),
                video_url = VALUES(video_url)";

        $stmt = $connect->prepare($sql);
        $stmt->bind_param("ssss", 
            $update_data['heading'],
            $update_data['subheading'],
            $update_data['hero_image'],
            $update_data['video_url']
        );

        if (!$stmt->execute()) {
            throw new Exception("Database error: " . $stmt->error);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Hero section updated successfully',
            'data' => $update_data
        ]);

    } else {
        throw new Exception('Invalid request method');
    }

} catch (Exception $e) {
    error_log("Hero section error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Make sure nothing is output after the JSON
exit;
?> 