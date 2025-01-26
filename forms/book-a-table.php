<?php
include '../db-config.php'; 

header('Content-Type: application/json'); // Ensure the response is in JSON format

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $connect->real_escape_string($_POST['name']);
    $email = $connect->real_escape_string($_POST['email']);
    $phone = $connect->real_escape_string($_POST['phone']);
    $date = $connect->real_escape_string($_POST['date']);
    $time = $connect->real_escape_string($_POST['time']);
    $people = (int)$connect->real_escape_string($_POST['people']);
    $message = $connect->real_escape_string($_POST['message']);

    $sql = "INSERT INTO bookings (name, email, phone, booking_date, booking_time, people, message)
            VALUES ('$name', '$email', '$phone', '$date', '$time', $people, '$message')";

if ($connect->query($sql) === TRUE) {
  echo "OK"; // This is what validate.js is expecting for success
} else {
  echo "Error: " . $connect->error; // Send error message in plain text
}
} else {
echo "Error: Invalid Request";
}
?>
