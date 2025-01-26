<?php
session_start();
require_once '../db-config.php';

// Check admin authentication
if (!isset($_SESSION['admin_id'])) {
    die('Access denied');
}

// Define required directories
$directories = [
    '../uploads',
    '../uploads/gallery',
    '../uploads/menu',
    '../uploads/hero',
    '../uploads/chefs'
];

// Create directories and set permissions
foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        if (!mkdir($dir, 0755, true)) {
            die("Failed to create directory: $dir");
        }
    }
    
    if (!is_writable($dir)) {
        if (!chmod($dir, 0755)) {
            die("Failed to set permissions on directory: $dir");
        }
    }
}

echo "Setup completed successfully!";
?> 