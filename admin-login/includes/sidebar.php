<?php
// Get the current page name for active menu highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar -->
<nav id="sidebar">
    <div class="admin-brand">
        <h2>Time Cafe</h2>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></p>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>" 
               href="<?php echo isset($isSection) ? '../dashboard.php' : 'dashboard.php'; ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $current_page === 'hero.php' ? 'active' : ''; ?>" 
               href="<?php echo isset($isSection) ? 'hero.php' : 'sections/hero.php'; ?>">
                <i class="bi bi-image"></i> Hero Section
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $current_page === 'menu.php' ? 'active' : ''; ?>" 
               href="../sections/menu.php">
                <i class="bi bi-menu-button-wide"></i> Menu
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $current_page === 'gallery.php' ? 'active' : ''; ?>" 
               href="../sections/gallery.php">
                <i class="bi bi-images"></i> Gallery
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $current_page === 'chefs.php' ? 'active' : ''; ?>" 
               href="../sections/chefs.php">
                <i class="bi bi-people"></i> Chefs
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $current_page === 'about.php' ? 'active' : ''; ?>" 
               href="../sections/about.php">
                <i class="bi bi-info-circle"></i> About
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="../logout.php">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </li>
    </ul>
</nav> 