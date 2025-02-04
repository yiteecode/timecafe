<?php

// Get the current page name for active menu highlighting
$current_page = basename($_SERVER['PHP_SELF']);

// Get current logo from settings
$logoStmt = $connect->prepare("SELECT setting_value FROM settings WHERE setting_key = 'brand_logo'");
$logoStmt->execute();
$logoResult = $logoStmt->get_result();
$currentLogo = $logoResult->fetch_assoc()['setting_value'] ?? 'time-logo.png';

// Fix logo path logic for both dashboard and section pages
if (isset($isSection)) {
    $logoUrl = '../../assets/img/' . htmlspecialchars($currentLogo) . '?v=' . time();
    $logoFallback = '../../assets/img/time-logo.png';
} else {
    $logoUrl = '../assets/img/' . htmlspecialchars($currentLogo) . '?v=' . time();
    $logoFallback = '../assets/img/time-logo.png';
}
?>

<!-- Sidebar -->
<nav id="sidebar">
    <div class="admin-brand">
        <div class="logo-container">
            <img src="<?php echo $logoUrl; ?>" 
                 alt="Time Cafe Logo" 
                 class="sidebar-logo"
                 onerror="this.src='<?php echo $logoFallback; ?>'">
                 <!-- <h2>Time Cafe</h2> -->
        </div>
        <button id="sidebar-toggle" class="btn btn-link text-dark p-0">
            <i class="bi bi-chevron-left fs-4"></i>
        </button>
    </div>

    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center <?php echo $current_page === 'admin_dashboard.php' ? 'active' : ''; ?>" 
                href="<?php echo isset($isSection) ? '../admin_dashboard.php' : 'admin_dashboard.php'; ?>">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="nav-section">
            <h6 class="section-title text-uppercase fw-bold">Content Management</h6>
        </li>

        <li class="nav-item">
            <a class="nav-link d-flex align-items-center <?php echo $current_page === 'hero.php' ? 'active' : ''; ?>" 
               href="<?php echo isset($isSection) ? 'hero.php' : 'sections/hero.php'; ?>">
                <i class="bi bi-image"></i>
                <span>Hero Section</span>
            </a>
        </li>

        <li class="nav-item">
        <a class="nav-link d-flex align-items-center <?php echo $current_page === 'menu.php' ? 'active' : ''; ?>" 
        href="<?php echo isset($isSection) ? 'menu.php' : 'sections/menu.php'; ?>">
            <!-- <a class="nav-link d-flex align-items-center <?php echo $current_page === 'menu.php' ? 'active' : ''; ?>" 
               href="../sections/menu.php"> -->
                <i class="bi bi-menu-button-wide"></i>
                <span>Menu</span>
            </a>
        </li>

        <li class="nav-item">
        <a class="nav-link d-flex align-items-center <?php echo $current_page === 'gallery.php' ? 'active' : ''; ?>" 
        href="<?php echo isset($isSection) ? 'gallery.php' : 'sections/gallery.php'; ?>">

            <!-- <a class="nav-link d-flex align-items-center <?php echo $current_page === 'gallery.php' ? 'active' : ''; ?>" 
               href="../sections/gallery.php"> -->
                <i class="bi bi-images"></i>
                <span>Gallery</span>
            </a>
        </li>

        <li class="nav-item">
        <a class="nav-link d-flex align-items-center <?php echo $current_page === 'chefs.php' ? 'active' : ''; ?>" 
        href="<?php echo isset($isSection) ? 'chefs.php' : 'sections/chefs.php'; ?>">

            <!-- <a class="nav-link d-flex align-items-center <?php echo $current_page === 'chefs.php' ? 'active' : ''; ?>" 
               href="../sections/chefs.php"> -->
                <i class="bi bi-people"></i>
                <span>Chefs</span>
            </a>
        </li>

        <li class="nav-item">
        <a class="nav-link d-flex align-items-center <?php echo $current_page === 'about.php' ? 'active' : ''; ?>" 
        href="<?php echo isset($isSection) ? 'about.php' : 'sections/about.php'; ?>">

            <!-- <a class="nav-link d-flex align-items-center <?php echo $current_page === 'about.php' ? 'active' : ''; ?>" 
               href="../sections/about.php"> -->
                <i class="bi bi-info-circle"></i>
                <span>About</span>
            </a>
        </li>

        <li class="nav-section">
            <h6 class="section-title text-uppercase fw-bold">Operations</h6>
        </li>

        <li class="nav-item">
        <a class="nav-link d-flex align-items-center <?php echo $current_page === 'menu.php' ? 'active' : ''; ?>" 
        href="<?php echo isset($isSection) ? 'contact_messages.php' : 'sections/contact_messages.php'; ?>">

            <!-- <a class="nav-link d-flex align-items-center" href="../sections/contact_messages.php"> -->
                <i class="bi bi-envelope"></i>
                <span>Contact Messages</span>
            </a>
        </li>

        <li class="nav-item">
        <a class="nav-link d-flex align-items-center <?php echo $current_page === 'bookings.php' ? 'active' : ''; ?>" 
        href="<?php echo isset($isSection) ? 'bookings.php' : 'sections/bookings.php'; ?>">

            <!-- <a class="nav-link d-flex align-items-center" href="../sections/bookings.php"> -->
                <i class="bi bi-calendar-check"></i>
                <span>Bookings</span>
            </a>
        </li>

        <li class="nav-item">
        <a class="nav-link d-flex align-items-center <?php echo $current_page === 'orders.php' ? 'active' : ''; ?>" 
        href="<?php echo isset($isSection) ? 'orders.php' : 'sections/orders.php'; ?>">

            <!-- <a class="nav-link d-flex align-items-center" href="../sections/orders.php"> -->
                <i class="bi bi-cart"></i>
                <span>Orders</span>
            </a>
        </li>

        <?php
            // Check if user is super admin
            $stmt = $connect->prepare("SELECT role FROM admins WHERE id = ?");
            $stmt->bind_param("i", $_SESSION['admin_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $admin = $result->fetch_assoc();

            if ($admin['role'] === 'super_admin'): 
            ?>
                <li class="nav-item">
                    <a href="<?php echo isset($isSection) ? 'users.php' : 'sections/users.php'; ?>" class="nav-link">
                        <i class="bi bi-people"></i>
                        <span>Users</span>
                    </a>
                </li>
            <?php endif; ?>

        <li class="nav-section">
            <h6 class="section-title text-uppercase fw-bold">System</h6>
        </li>

        <?php
            // Check if user is super admin
            $stmt = $connect->prepare("SELECT role FROM admins WHERE id = ?");
            $stmt->bind_param("i", $_SESSION['admin_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $admin = $result->fetch_assoc();

            if ($admin['role'] === 'super_admin'): 
            ?>
                <li class="nav-item">
                    <a href="<?php echo isset($isSection) ? 'settings.php' : 'sections/settings.php'; ?>" class="nav-link">
                        <i class="bi bi-gear"></i>
                        <span>Settings</span>
                    </a>
                </li>
            <?php endif; ?>

        <li class="nav-item">
            <a class="nav-link d-flex align-items-center" href="../admin-login/logout.php">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');
    const header = document.querySelector('.main-header');
    const toggleIcon = document.querySelector('#sidebar-toggle i');

    function toggleSidebar() {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
        header.classList.toggle('expanded');
        
        // Toggle icon
        if (sidebar.classList.contains('collapsed')) {
            toggleIcon.classList.remove('bi-chevron-left');
            toggleIcon.classList.add('bi-chevron-right');
        } else {
            toggleIcon.classList.remove('bi-chevron-right');
            toggleIcon.classList.add('bi-chevron-left');
        }
        
        // Store the state
        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
    }

    // Add click event listener
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', toggleSidebar);
    }

    // Check and restore sidebar state on page load
    const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (sidebarCollapsed) {
        sidebar.classList.add('collapsed');
        mainContent.classList.add('expanded');
        header.classList.add('expanded');
        toggleIcon.classList.remove('bi-chevron-left');
        toggleIcon.classList.add('bi-chevron-right');
    }
});

function updateSidebarLogo(newLogoPath) {
    const sidebarLogo = document.getElementById('sidebarLogo');
    if (sidebarLogo) {
        const basePath = '<?php echo $logoPath; ?>';
        sidebarLogo.src = basePath + newLogoPath + '?v=' + new Date().getTime();
    }
}
</script> 