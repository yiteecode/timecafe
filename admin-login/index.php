<?php
session_start();

// Simple redirect to login page if not authenticated
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Redirect to admin dashboard if authenticated
header('Location: admin_dashboard.php');
exit;

// Include header
include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <h1 class="mb-4">Content Management</h1>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs" id="contentTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="hero-tab" data-bs-toggle="tab" href="#hero" role="tab">Hero Section</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="menu-tab" data-bs-toggle="tab" href="#menu" role="tab">Menu</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="gallery-tab" data-bs-toggle="tab" href="#gallery" role="tab">Gallery</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="chefs-tab" data-bs-toggle="tab" href="#chefs" role="tab">Chefs</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="about-tab" data-bs-toggle="tab" href="#about" role="tab">About</a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content mt-4" id="contentTabsContent">
        <div class="tab-pane fade show active" id="hero" role="tabpanel">
            <?php include 'sections/hero.php'; ?>
        </div>
        <div class="tab-pane fade" id="menu" role="tabpanel">
            <?php include 'sections/menu.php'; ?>
        </div>
        <div class="tab-pane fade" id="gallery" role="tabpanel">
            <?php include 'sections/gallery.php'; ?>
        </div>
        <div class="tab-pane fade" id="chefs" role="tabpanel">
            <?php include 'sections/chefs.php'; ?>
        </div>
        <div class="tab-pane fade" id="about" role="tabpanel">
            <?php include 'sections/about.php'; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 