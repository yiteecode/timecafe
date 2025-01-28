<?php
// Create necessary directories if they don't exist
$directories = [
    '../uploads',
    '../uploads/gallery',
    '../uploads/menu',
    '../uploads/chefs',
    '../uploads/hero'
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}

echo "Directories created successfully!";
?> 