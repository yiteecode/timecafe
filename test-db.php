<?php
include 'db-config.php';
$result = $connect->query("SHOW TABLES");
if ($result) {
    echo "Database connection successful.";
} else {
    echo "Database connection failed: " . $connect->error;
}
?>