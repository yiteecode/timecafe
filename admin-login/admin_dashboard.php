<?php
session_start();
require_once '../db-config.php';
require_once 'includes/auth_check.php';


// Get counts for dashboard stats
$stats = [
    'orders' => mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as count FROM orders"))['count'] ?? 0,
    'messages' => mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as count FROM contact_messages"))['count'] ?? 0,
    'chefs' => mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) as count FROM chefs"))['count'] ?? 0
];

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Get admin info
$admin_username = $_SESSION['admin_username'];

// Include header
include 'includes/header.php';
?>

<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</h1>
                        <p class="col-md-8">Here's what's happening with your cafe today.</p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <button class="btn btn-primary" onclick="location.href='sections/menu.php'">
                            <i class="bi bi-plus-circle"></i> Add New Item
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <!-- Total Orders Stats -->
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-card-body">
                    <div class="stat-icon bg-primary">
                        <i class="bi bi-cart"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['orders']; ?></h3>
                        <p>Total Orders</p>
                    </div>
                </div>
                <div class="stat-card-footer">
                    <a href="sections/orders.php">View Details <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Contact Messages Stats -->
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-card-body">
                    <div class="stat-icon bg-success">
                        <i class="bi bi-envelope"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['messages']; ?></h3>
                        <p>Contact Messages</p>
                    </div>
                </div>
                <div class="stat-card-footer">
                    <a href="sections/contact_messages.php">View Details <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Chefs Stats -->
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-card-body">
                    <div class="stat-icon bg-info">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['chefs']; ?></h3>
                        <p>Chefs</p>
                    </div>
                </div>
                <div class="stat-card-footer">
                    <a href="sections/chefs.php">View Details <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-3">
                            <a href="sections/hero.php" class="quick-action-card">
                                <i class="bi bi-image"></i>
                                <span>Update Hero Section</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="sections/menu.php" class="quick-action-card">
                                <i class="bi bi-menu-button-wide"></i>
                                <span>Manage Menu</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="sections/gallery.php" class="quick-action-card">
                                <i class="bi bi-images"></i>
                                <span>Update Gallery</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="sections/about.php" class="quick-action-card">
                                <i class="bi bi-info-circle"></i>
                                <span>Edit About</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Dashboard Specific Styles */
.welcome-card {
    background: linear-gradient(135deg, #2c3e50, #3498db);
    color: white;
    padding: 2rem;
    border-radius: 1rem;
    margin-top: 1rem;
}

.welcome-card h1 {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.stat-card {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: transform 0.2s;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-card-body {
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    width: 4rem;
    height: 4rem;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-icon i {
    font-size: 1.5rem;
    color: white;
}

.stat-info h3 {
    font-size: 1.75rem;
    margin: 0;
    font-weight: 600;
}

.stat-info p {
    margin: 0;
    color: #6c757d;
}

.stat-card-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid rgba(0,0,0,0.05);
}

.stat-card-footer a {
    color: #2c3e50;
    text-decoration: none;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.quick-action-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    background: #f8f9fa;
    border-radius: 1rem;
    text-decoration: none;
    color: #2c3e50;
    transition: all 0.2s;
    border: 1px solid rgba(0,0,0,0.05);
}

.quick-action-card:hover {
    background: #e9ecef;
    transform: translateY(-5px);
    color: #2c3e50;
}

.quick-action-card i {
    font-size: 2rem;
    margin-bottom: 1rem;
}

.quick-action-card span {
    font-weight: 500;
}

@media (max-width: 768px) {
    .welcome-card {
        text-align: center;
    }
    
    .welcome-card .btn {
        margin-top: 1rem;
    }
}
</style>

<?php include 'includes/footer.php'; ?>