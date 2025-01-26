<?php
session_start();
require_once '../../db-config.php';

if (!isset($_SESSION['admin_id'])) {
    header('HTTP/1.1 403 Forbidden');
    exit('Access denied');
}

function handleHeroUpdate() {
    global $connect;
    $response = ['success' => false, 'message' => '', 'data' => null];
    
    try {
        $heading = filter_var($_POST['heading'], FILTER_SANITIZE_STRING);
        $subheading = filter_var($_POST['subheading'], FILTER_SANITIZE_STRING);
        
        // Handle image upload
        $hero_image = null;
        if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] === 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            if (!in_array($_FILES['hero_image']['type'], $allowed_types)) {
                throw new Exception('Invalid image type. Allowed types: JPG, PNG, WEBP');
            }
            
            if ($_FILES['hero_image']['size'] > $max_size) {
                throw new Exception('Image size too large. Maximum size: 5MB');
            }
            
            $filename = 'hero_' . uniqid() . '_' . basename($_FILES['hero_image']['name']);
            $upload_path = '../../uploads/hero/' . $filename;
            
            if (move_uploaded_file($_FILES['hero_image']['tmp_name'], $upload_path)) {
                $hero_image = $filename;
            }
        }
        
        // Handle video upload
        $video_url = null;
        if (isset($_FILES['hero_video']) && $_FILES['hero_video']['error'] === 0) {
            $allowed_types = ['video/mp4', 'video/webm'];
            $max_size = 50 * 1024 * 1024; // 50MB
            
            if (!in_array($_FILES['hero_video']['type'], $allowed_types)) {
                throw new Exception('Invalid video type. Allowed types: MP4, WEBM');
            }
            
            if ($_FILES['hero_video']['size'] > $max_size) {
                throw new Exception('Video size too large. Maximum size: 50MB');
            }
            
            $filename = 'hero_' . uniqid() . '_' . basename($_FILES['hero_video']['name']);
            $upload_path = '../../uploads/hero/' . $filename;
            
            if (move_uploaded_file($_FILES['hero_video']['tmp_name'], $upload_path)) {
                $video_url = $filename;
            }
        }
        
        // Update database
        $query = "UPDATE hero_section SET 
                 heading = ?, 
                 subheading = ?";
        $params = [$heading, $subheading];
        
        if ($hero_image) {
            $query .= ", hero_image = ?";
            $params[] = $hero_image;
        }
        
        if ($video_url) {
            $query .= ", video_url = ?";
            $params[] = $video_url;
        }
        
        $query .= " WHERE id = 1";
        
        $stmt = $connect->prepare($query);
        $stmt->execute($params);
        
        // Delete old files if new ones were uploaded
        if ($hero_image && !empty($_POST['old_image'])) {
            $old_image_path = '../../uploads/hero/' . $_POST['old_image'];
            if (file_exists($old_image_path)) {
                unlink($old_image_path);
            }
        }
        
        if ($video_url && !empty($_POST['old_video'])) {
            $old_video_path = '../../uploads/hero/' . $_POST['old_video'];
            if (file_exists($old_video_path)) {
                unlink($old_video_path);
            }
        }
        
        $response['success'] = true;
        $response['message'] = 'Changes saved successfully!';
        
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
    
    return $response;
}

// Handle AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    echo json_encode(handleHeroUpdate());
    exit;
}
?> 