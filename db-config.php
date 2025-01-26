<?php
// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'time-cafe';


// Establish a connection
$connect = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

// Set the character set to avoid encoding issues
$connect->set_charset("utf8");
?>
