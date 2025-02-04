<?php
session_start();
require_once '../../db-config.php';

header('Content-Type: application/json');

// Check if user is logged in and is super admin
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$stmt = $connect->prepare("SELECT role FROM admins WHERE id = ?");
$stmt->bind_param("i", $_SESSION['admin_id']);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if ($admin['role'] !== 'super_admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

try {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'create':
            if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['role'])) {
                throw new Exception('All fields are required');
            }

            // Check if username or email already exists
            $stmt = $connect->prepare("SELECT id FROM admins WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $_POST['username'], $_POST['email']);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                throw new Exception('Username or email already exists');
            }

            // Create new user
            $stmt = $connect->prepare("INSERT INTO admins (username, email, password, role) VALUES (?, ?, ?, ?)");
            $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt->bind_param("ssss", $_POST['username'], $_POST['email'], $hashed_password, $_POST['role']);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to create user');
            }

            echo json_encode([
                'success' => true,
                'message' => 'User created successfully'
            ]);
            break;

        case 'get':
            if (empty($_POST['id'])) {
                throw new Exception('User ID is required');
            }

            $stmt = $connect->prepare("SELECT id, username, email, role FROM admins WHERE id = ?");
            $stmt->bind_param("i", $_POST['id']);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();

            if (!$user) {
                throw new Exception('User not found');
            }

            echo json_encode([
                'success' => true,
                'user' => $user
            ]);
            break;

        case 'update':
            if (empty($_POST['id']) || empty($_POST['username']) || empty($_POST['email']) || empty($_POST['role'])) {
                throw new Exception('Required fields are missing');
            }

            // Check if username or email already exists for other users
            $stmt = $connect->prepare("SELECT id FROM admins WHERE (username = ? OR email = ?) AND id != ?");
            $stmt->bind_param("ssi", $_POST['username'], $_POST['email'], $_POST['id']);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                throw new Exception('Username or email already exists');
            }

            // Update user
            if (!empty($_POST['password'])) {
                $stmt = $connect->prepare("UPDATE admins SET username = ?, email = ?, password = ?, role = ? WHERE id = ?");
                $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $stmt->bind_param("ssssi", $_POST['username'], $_POST['email'], $hashed_password, $_POST['role'], $_POST['id']);
            } else {
                $stmt = $connect->prepare("UPDATE admins SET username = ?, email = ?, role = ? WHERE id = ?");
                $stmt->bind_param("sssi", $_POST['username'], $_POST['email'], $_POST['role'], $_POST['id']);
            }
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to update user');
            }

            echo json_encode([
                'success' => true,
                'message' => 'User updated successfully'
            ]);
            break;

        case 'delete':
            if (empty($_POST['id'])) {
                throw new Exception('User ID is required');
            }

            // Prevent deleting self
            if ($_POST['id'] == $_SESSION['admin_id']) {
                throw new Exception('Cannot delete your own account');
            }

            $stmt = $connect->prepare("DELETE FROM admins WHERE id = ?");
            $stmt->bind_param("i", $_POST['id']);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to delete user');
            }

            echo json_encode([
                'success' => true,
                'message' => 'User deleted successfully'
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