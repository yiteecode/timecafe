<?php
require_once '../db-config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('HTTP/1.1 403 Forbidden');
    exit('Access denied');
}

header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

try {
    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        
        if (!in_array($file['type'], $allowed_types)) {
            throw new Exception('Invalid file type');
        }
        
        $filename = uniqid() . '_' . $file['name'];
        $upload_path = '../uploads/gallery/' . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            $stmt = $connect->prepare("INSERT INTO gallery (image) VALUES (?)");
            $stmt->execute([$filename]);
            
            $response['success'] = true;
            $response['message'] = 'File uploaded successfully';
        } else {
            throw new Exception('Failed to upload file');
        }
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?> 