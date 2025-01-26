<?php
// Define upload directories
$directories = [
    '../uploads/hero',
    '../uploads/menu',
    '../uploads/gallery',
    '../uploads/testimonials',
    '../uploads/about'
];

// Create directories with proper permissions
foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}

echo "Directories created successfully!";
?> 