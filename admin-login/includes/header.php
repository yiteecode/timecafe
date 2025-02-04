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
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <!-- Top Header -->
    <header class="main-header">
        <div class="header-left">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" placeholder="Search..." class="form-control">
            </div>
        </div>
        <div class="header-right">
            <div class="header-icons">
                <div class="header-icon me-4 dropdown">
                    <div class="notification-icon" data-bs-toggle="dropdown">
                        <i class="bi bi-bell"></i>
                        <span class="notification-badge" id="orderNotificationBadge">0</span>
                    </div>
                    <div class="dropdown-menu notification-dropdown" id="notificationDropdown">
                        <div class="dropdown-item text-center">Loading...</div>
                    </div>
                </div>
                <div class="header-icon me-4">
                    <i class="bi bi-envelope"></i>
                    <span class="notification-badge" id="messageNotificationBadge">0</span>
                </div>
            </div>
            <div class="user-profile dropdown">
                <div class="profile-icon" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle fs-4"></i>
                </div>
                
                <!-- User Dropdown Menu -->
                <div class="dropdown-menu user-dropdown">
                    <div class="user-info text-center p-3 border-bottom">
                        <i class="bi bi-person-circle avatar-large mb-2"></i>
                        <h6 class="mb-1"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></h6>
                        <p class="text-muted small mb-0">Administrator</p>
                    </div>
                    <div class="dropdown-items p-2">
                        <a class="dropdown-item d-flex align-items-center py-2" href="change-password.php">
                            <i class="bi bi-arrow-repeat me-2"></i>
                            Change Password
                        </a>
                        <a class="dropdown-item d-flex align-items-center py-2" href="../logout.php">
                            <i class="bi bi-box-arrow-right me-2"></i>
                            Log Out
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Include Sidebar -->
    <?php 
    if (isset($isSection)) {
        include_once '../includes/sidebar.php';
    } else {
        include_once 'includes/sidebar.php';
    }
    ?>
   
    <!-- Main Content Wrapper -->
    <main class="main-content">

    <!-- Add this script at the bottom of the file, just before </body> -->
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
    </script>
    <script src="<?php echo isset($isSection) ? '../js/notifications.js' : 'js/notifications.js'; ?>"></script>
</body>
</html>
