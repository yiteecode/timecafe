<?php
include '../db-config.php'; // Ensure this file path is correct

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$response = ["status" => "error", "message" => "Something went wrong!"];

// Test database connection
if (!$connect) {
    die(json_encode(["status" => "error", "message" => "Database connection failed: " . $connect->connect_error]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form data
    $name = $connect->real_escape_string(trim($_POST['name']));
    $email = $connect->real_escape_string(trim($_POST['email']));
    $subject = $connect->real_escape_string(trim($_POST['subject']));
    $message = $connect->real_escape_string(trim($_POST['message']));

    // Validate required fields
    if (!empty($name) && !empty($email) && !empty($subject) && !empty($message)) {
        // Prepare an SQL statement to insert the data
        $sql = "INSERT INTO contact_messages (name, email, subject, message) VALUES ('$name', '$email', '$subject', '$message')";

        if ($connect->query($sql) === TRUE) {
            $response = ["status" => "success", "message" => "Your message has been sent successfully!"];
        } else {
            $response = ["status" => "error", "message" => "Database error: " . $connect->error];
        }
    } else {
        $response = ["status" => "error", "message" => "All fields are required!"];
    }
}

// Close the database connection
$connect->close();

// Return the JSON response
echo json_encode($response);
