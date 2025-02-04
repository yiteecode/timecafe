<?php
session_start();
require_once '../../db-config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

try {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'get_message':
            if (empty($_POST['id'])) {
                throw new Exception('Message ID is required');
            }

            $stmt = $connect->prepare("SELECT * FROM contact_messages WHERE id = ?");
            $stmt->bind_param("i", $_POST['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $message = $result->fetch_assoc();

            if (!$message) {
                throw new Exception('Message not found');
            }

            echo json_encode([
                'success' => true,
                'message' => $message
            ]);
            break;

        case 'toggle_read':
            if (empty($_POST['id'])) {
                throw new Exception('Message ID is required');
            }

            $stmt = $connect->prepare("UPDATE contact_messages SET is_read = NOT is_read WHERE id = ?");
            $stmt->bind_param("i", $_POST['id']);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to update message status');
            }

            echo json_encode([
                'success' => true,
                'message' => 'Message status updated'
            ]);
            break;

        case 'delete':
            if (empty($_POST['id'])) {
                throw new Exception('Message ID is required');
            }

            $stmt = $connect->prepare("DELETE FROM contact_messages WHERE id = ?");
            $stmt->bind_param("i", $_POST['id']);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to delete message');
            }

            echo json_encode([
                'success' => true,
                'message' => 'Message deleted successfully'
            ]);
            break;

        case 'delete_multiple':
            if (empty($_POST['ids'])) {
                throw new Exception('No messages selected');
            }

            $ids = json_decode($_POST['ids'], true);
            if (!is_array($ids)) {
                throw new Exception('Invalid message IDs');
            }

            // Create placeholders for the IN clause
            $placeholders = str_repeat('?,', count($ids) - 1) . '?';
            $stmt = $connect->prepare("DELETE FROM contact_messages WHERE id IN ($placeholders)");
            
            // Create array of types for bind_param
            $types = str_repeat('i', count($ids));
            $stmt->bind_param($types, ...$ids);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to delete messages');
            }

            echo json_encode([
                'success' => true,
                'message' => 'Messages deleted successfully'
            ]);
            break;

        default:
            throw new Exception('Invalid action');
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 