<?php
session_start();
require_once '../db-config.php';

// Debug log
error_log('Gallery items request received');

if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

header('Content-Type: application/json');

try {
    // Debug log
    error_log('Fetching gallery items from database');
    
    $stmt = $connect->prepare("
        SELECT id, image, title, description, created_at 
        FROM gallery 
        ORDER BY created_at DESC
    ");
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to execute query: ' . $connect->error);
    }
    
    $result = $stmt->get_result();
    $items = [];
    
    while ($row = $result->fetch_assoc()) {
        // Verify image exists
        $imagePath = '../uploads/gallery/' . $row['image'];
        if (file_exists($imagePath)) {
            $items[] = [
                'id' => $row['id'],
                'image' => $row['image'],
                'title' => $row['title'],
                'description' => $row['description'],
                'created_at' => $row['created_at']
            ];
        }
    }
    
    // Debug log
    error_log('Found ' . count($items) . ' gallery items');
    
    echo json_encode([
        'success' => true,
        'data' => $items,
        'message' => count($items) > 0 ? '' : 'No gallery items found'
    ]);

} catch (Exception $e) {
    error_log('Error in get_gallery_items.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$stmt->close();
$connect->close();
?> 