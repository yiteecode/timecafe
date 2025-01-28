<?php
if (!isset($_SESSION['admin_id'])) {
    // Store the requested URL for redirect after login
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    
    // Get the relative path to login.php
    $path = str_repeat('../', substr_count($_SERVER['PHP_SELF'], '/') - 3);
    header('Location: ' . $path . 'admin-login/login.php');
    exit;
}

// Validate session data
if (!isset($_SESSION['admin_username'])) {
    session_destroy();
    $path = str_repeat('../', substr_count($_SERVER['PHP_SELF'], '/') - 3);
    header('Location: ' . $path . 'admin-login/login.php');
    exit;
} 