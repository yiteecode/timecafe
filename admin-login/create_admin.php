<?php
require_once '../db-config.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // New admin details
    $email = 'admin@timecafe.com';
    $password = 'admin123';
    $username = 'Admin';
    
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // First, check if admin already exists
    $check = $connect->prepare("SELECT id FROM admins WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows > 0) {
        // Update existing admin
        $stmt = $connect->prepare("UPDATE admins SET password = ?, username = ? WHERE email = ?");
        $stmt->bind_param("sss", $hashed_password, $username, $email);
    } else {
        // Create new admin
        $stmt = $connect->prepare("INSERT INTO admins (email, password, username, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $email, $hashed_password, $username);
    }
    
    if ($stmt->execute()) {
        echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;'>";
        echo "<h2 style='color: #2c3e50;'>Admin User Created Successfully!</h2>";
        echo "<p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>";
        echo "<p><strong>Password:</strong> " . htmlspecialchars($password) . "</p>";
        echo "<p><strong>Password Hash:</strong> " . $hashed_password . "</p>";
        echo "<p style='color: red;'><strong>Important:</strong> Please delete this file after use!</p>";
        echo "</div>";
    } else {
        throw new Exception("Error executing query: " . $stmt->error);
    }
    
} catch (Exception $e) {
    echo "<div style='color: red;'>";
    echo "Error: " . htmlspecialchars($e->getMessage());
    echo "</div>";
}

$connect->close();
?> 