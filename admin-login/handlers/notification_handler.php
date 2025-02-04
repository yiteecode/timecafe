<?php
session_start();
require_once '../../db-config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    // Get unread orders count
    $ordersStmt = $connect->prepare("
        SELECT COUNT(*) as count, MAX(id) as latest_id 
        FROM orders 
        WHERE status = 'pending'
    ");
    $ordersStmt->execute();
    $ordersResult = $ordersStmt->get_result()->fetch_assoc();

    // Get unread messages count
    $messagesStmt = $connect->prepare("
        SELECT COUNT(*) as count 
        FROM contact_messages 
        WHERE is_read = 0
    ");
    $messagesStmt->execute();
    $messagesCount = $messagesStmt->get_result()->fetch_assoc()['count'];

    // Get recent orders
    $recentOrdersStmt = $connect->prepare("
        SELECT o.*, 
               COUNT(o.id) as items,
               TIMESTAMPDIFF(MINUTE, o.created_at, NOW()) as minutes_ago
        FROM orders o
        WHERE o.status = 'pending'
        GROUP BY o.id
        ORDER BY o.created_at DESC
        LIMIT 5
    ");
    $recentOrdersStmt->execute();
    $recentOrders = $recentOrdersStmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Format recent orders for display
    $formattedOrders = array_map(function($order) {
        return [
            'id' => $order['id'],
            'customer_name' => $order['customer_name'],
            'items' => $order['items'],
            'time_ago' => formatTimeAgo($order['minutes_ago']),
        ];
    }, $recentOrders);

    echo json_encode([
        'orders' => $ordersResult['count'],
        'messages' => $messagesCount,
        'newOrders' => $ordersResult['count'],
        'latestOrderId' => $ordersResult['latest_id'],
        'recentOrders' => $formattedOrders
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

function formatTimeAgo($minutes) {
    if ($minutes < 1) {
        return 'Just now';
    } elseif ($minutes < 60) {
        return $minutes . ' min ago';
    } elseif ($minutes < 1440) {
        $hours = floor($minutes / 60);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } else {
        $days = floor($minutes / 1440);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    }
} 