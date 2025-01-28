<?php
session_start();
require_once '../../db-config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Log the login attempt
    error_log("Login attempt with email: " . $email);

    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = 'Please enter both email and password';
        header('Location: ../login.php');
        exit;
    }

    try {
        // Get admin user from database
        $stmt = $connect->prepare("SELECT * FROM admins WHERE email = ? LIMIT 1");
        if (!$stmt) {
            throw new Exception("Database prepare failed: " . $connect->error);
        }

        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
            throw new Exception("Database execute failed: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();

        // Debug log
        error_log("Admin found: " . ($admin ? "Yes" : "No"));
        if ($admin) {
            error_log("Stored password hash: " . $admin['password']);
            error_log("Password verification result: " . (password_verify($password, $admin['password']) ? "Success" : "Failed"));
        }

        if ($admin && password_verify($password, $admin['password'])) {
            // Login successful
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'] ?? $admin['email'];
            
            error_log("Login successful for user ID: " . $admin['id']);
            
            // Clear any output buffers
            while (ob_get_level()) {
                ob_end_clean();
            }

            // Redirect to dashboard
            header("Location: ../admin_dashboard.php");
            exit();
        } else {
            error_log("Login failed - Invalid credentials");
            $_SESSION['login_error'] = 'Invalid email or password';
            header('Location: ../login.php');
            exit();
        }

    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        $_SESSION['login_error'] = 'System error occurred. Please try again.';
        header('Location: ../login.php');
        exit();
    }
}

// If not POST request, redirect to login
header('Location: ../login.php');
exit(); 