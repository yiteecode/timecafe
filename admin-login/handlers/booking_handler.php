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
        case 'get_booking':
            if (empty($_POST['id'])) {
                throw new Exception('Booking ID is required');
            }

            $stmt = $connect->prepare("SELECT * FROM bookings WHERE id = ?");
            $stmt->bind_param("i", $_POST['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $booking = $result->fetch_assoc();

            if (!$booking) {
                throw new Exception('Booking not found');
            }

            echo json_encode([
                'success' => true,
                'booking' => $booking
            ]);
            break;

        case 'update_status':
            if (empty($_POST['id']) || empty($_POST['status'])) {
                throw new Exception('Booking ID and status are required');
            }

            $allowed_statuses = ['pending', 'accepted', 'rejected'];
            if (!in_array($_POST['status'], $allowed_statuses)) {
                throw new Exception('Invalid status');
            }

            $stmt = $connect->prepare("UPDATE bookings SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $_POST['status'], $_POST['id']);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to update booking status');
            }

            echo json_encode([
                'success' => true,
                'message' => 'Booking status updated successfully'
            ]);
            break;

        case 'delete':
            if (empty($_POST['id'])) {
                throw new Exception('Booking ID is required');
            }

            $stmt = $connect->prepare("DELETE FROM bookings WHERE id = ?");
            $stmt->bind_param("i", $_POST['id']);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to delete booking');
            }

            echo json_encode([
                'success' => true,
                'message' => 'Booking deleted successfully'
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