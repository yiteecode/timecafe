<?php
// Get the current page name for active menu highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar -->
<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar">
    <div class="position-sticky">
        <div class="admin-brand p-3">
            <h2>Time Cafe</h2>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></p>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'admin_dashboard.php' ? 'active' : ''; ?>" 
                   href="admin_dashboard.php">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'manage_content.php' ? 'active' : ''; ?>" 
                   href="manage_content.php">
                    <i class="bi bi-pencil-square"></i> Manage Content
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'manage_menu.php' ? 'active' : ''; ?>" 
                   href="manage_menu.php">
                    <i class="bi bi-menu-button-wide"></i> Manage Menu
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'manage_reservations.php' ? 'active' : ''; ?>" 
                   href="manage_reservations.php">
                    <i class="bi bi-calendar-check"></i> Reservations
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'manage_gallery.php' ? 'active' : ''; ?>" 
                   href="manage_gallery.php">
                    <i class="bi bi-images"></i> Gallery
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</nav> 