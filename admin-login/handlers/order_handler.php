<?php
session_start();
require_once '../../db-config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

try {
    if (empty($_POST['id']) || empty($_POST['status'])) {
        throw new Exception('Order ID and status are required');
    }

    $allowed_statuses = ['pending', 'processing', 'completed', 'cancelled'];
    if (!in_array($_POST['status'], $allowed_statuses)) {
        throw new Exception('Invalid status');
    }

    $stmt = $connect->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $_POST['status'], $_POST['id']);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to update order status');
    }

    echo json_encode([
        'success' => true,
        'message' => 'Order status updated successfully'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 