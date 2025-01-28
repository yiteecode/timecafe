<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time Cafe - Admin Dashboard</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo isset($isSection) ? '../css/admin.css' : 'css/admin.css'; ?>" rel="stylesheet">
</head>
<body>
    <!-- Top Header -->
    <header class="main-header">
        <div class="header-left">
            <button id="sidebar-toggle" class="btn">
                <i class="bi bi-list"></i>
            </button>
            <h1 class="header-title">Admin Panel</h1>
        </div>
        <div class="header-right">
            <span class="user-welcome">Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
            <a href="<?php echo isset($isSection) ? '../logout.php' : 'logout.php'; ?>" class="btn btn-outline-light">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </header>

    <!-- Sidebar -->
    <nav id="sidebar" class="sidebar">
        <div class="sidebar-header">
            <h2 class="logo">Time Cafe</h2>
        </div>
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a href="<?php echo isset($isSection) ? '../admin_dashboard.php' : 'admin_dashboard.php'; ?>" class="nav-link">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?php echo isset($isSection) ? 'hero.php' : 'sections/hero.php'; ?>" class="nav-link">
                    <i class="bi bi-image"></i>
                    <span>Hero Section</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?php echo isset($isSection) ? 'menu.php' : 'sections/menu.php'; ?>" class="nav-link">
                    <i class="bi bi-menu-button-wide"></i>
                    <span>Menu</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?php echo isset($isSection) ? 'gallery.php' : 'sections/gallery.php'; ?>" class="nav-link">
                    <i class="bi bi-images"></i>
                    <span>Gallery</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?php echo isset($isSection) ? 'chefs.php' : 'sections/chefs.php'; ?>" class="nav-link">
                    <i class="bi bi-people"></i>
                    <span>Chefs</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?php echo isset($isSection) ? 'about.php' : 'sections/about.php'; ?>" class="nav-link">
                    <i class="bi bi-info-circle"></i>
                    <span>About</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Content Wrapper -->
    <main class="main-content">
