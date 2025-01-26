<?php
session_start();
require_once '../db-config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Create necessary directories
$directories = [
    '../uploads/gallery',
    '../uploads/menu',
    '../uploads/hero',
    '../uploads/chefs'
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Helper functions
function getChefs() {
    global $connect;
    try {
        $stmt = $connect->prepare("
            SELECT * FROM chefs 
            WHERE active = 1 
            ORDER BY sort_order DESC, created_at DESC
        ");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        error_log("Error getting chefs: " . $e->getMessage());
        return [];
    }
}

function getHeroContent() {
    global $connect;
    try {
        $stmt = $connect->prepare("SELECT * FROM hero_section WHERE id = 1");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    } catch (Exception $e) {
        return null;
    }
}

function getMenuCategories() {
    global $connect;
    try {
        $stmt = $connect->prepare("SELECT * FROM menu_categories ORDER BY sort_order");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function getGalleryItems() {
    global $connect;
    try {
        $stmt = $connect->prepare("SELECT * FROM gallery WHERE active = 1 ORDER BY sort_order");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function getTestimonials() {
    global $connect;
    try {
        $stmt = $connect->prepare("SELECT * FROM testimonials WHERE active = 1");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function saveMenuItem($data) {
    global $connect;
    try {
        $stmt = $connect->prepare("INSERT INTO menu_items (category_id, name, description, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("issd", $data['category_id'], $data['name'], $data['description'], $data['price']);
        return $stmt->execute();
    } catch (Exception $e) {
        throw new Exception("Error saving menu item: " . $e->getMessage());
    }
}

function deleteGalleryItem($id) {
    global $connect;
    try {
        // Get image filename first
        $stmt = $connect->prepare("SELECT image FROM gallery WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $item = $result->fetch_assoc();

        // Delete file if exists
        if ($item && $item['image']) {
            $filepath = '../uploads/gallery/' . $item['image'];
            if (file_exists($filepath)) {
                unlink($filepath);
            }
        }

        // Delete database record
        $stmt = $connect->prepare("DELETE FROM gallery WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    } catch (Exception $e) {
        throw new Exception("Error deleting gallery item: " . $e->getMessage());
    }
}

function getAboutContent() {
    global $connect;
    try {
        $stmt = $connect->prepare("SELECT * FROM about_section WHERE id = 1");
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    } catch (Exception $e) {
        error_log("Error getting about content: " . $e->getMessage());
        return [];
    }
}

function getAboutFeatures() {
    global $connect;
    try {
        $stmt = $connect->prepare("
            SELECT * FROM about_features 
            WHERE active = 1 
            ORDER BY sort_order, created_at DESC
        ");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        error_log("Error getting about features: " . $e->getMessage());
        return [];
    }
}

// Handle AJAX requests
if (isset($_POST['ajax_action'])) {
    header('Content-Type: application/json');
    $response = ['success' => false, 'message' => ''];
    
    try {
        switch ($_POST['ajax_action']) {
            case 'preview_hero':
                $response['data'] = previewHeroSection($_POST);
                $response['success'] = true;
                break;
            case 'save_menu_item':
                saveMenuItem($_POST);
                $response['success'] = true;
                $response['message'] = 'Menu item saved successfully';
                break;
            case 'delete_gallery_item':
                deleteGalleryItem($_POST['id']);
                $response['success'] = true;
                $response['message'] = 'Gallery item deleted successfully';
                break;
        }
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
    
    echo json_encode($response);
    exit;
}

// Get current content
$hero_content = getHeroContent();
$menu_categories = getMenuCategories();
$gallery_items = getGalleryItems();
$testimonials = getTestimonials();
$chefs = getChefs();
$about_content = getAboutContent();
$about_features = getAboutFeatures();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Content - Time Cafe</title>
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
    <link href="../assets/vendor/dropzone/dropzone.min.css" rel="stylesheet">
    <link href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" rel="stylesheet" type="text/css" />
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Include sidebar -->
            <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1>Manage Content</h1>
                </div>

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success">
                        <?php 
                        echo $_SESSION['success_message'];
                        unset($_SESSION['success_message']);
                        ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger">
                        <?php 
                        echo $_SESSION['error_message'];
                        unset($_SESSION['error_message']);
                        ?>
                    </div>
                <?php endif; ?>

                <!-- Updated tabs -->
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

                <div class="tab-content mt-4" id="contentTabsContent">
                    <!-- Hero Section Tab -->
                    <div class="tab-pane fade show active" id="hero" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <form id="heroForm" action="handlers/hero_handler.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="old_image" value="<?php echo htmlspecialchars($hero_content['hero_image'] ?? ''); ?>">
                                    <input type="hidden" name="old_video" value="<?php echo htmlspecialchars($hero_content['video_url'] ?? ''); ?>">
                                    
                                    <div class="mb-3">
                                        <label for="hero_heading" class="form-label">Heading</label>
                                        <input type="text" class="form-control" id="hero_heading" name="heading" 
                                               value="<?php echo htmlspecialchars($hero_content['heading'] ?? ''); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="hero_subheading" class="form-label">Subheading</label>
                                        <textarea class="form-control" id="hero_subheading" name="subheading" rows="3"><?php echo htmlspecialchars($hero_content['subheading'] ?? ''); ?></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="hero_image" class="form-label">Hero Background Image</label>
                                        <input type="file" class="form-control" id="hero_image" name="hero_image" accept="image/*">
                                        <small class="text-muted">Recommended size: 1920x1080px. Max size: 5MB</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="hero_video" class="form-label">Hero Video</label>
                                        <input type="file" class="form-control" id="hero_video" name="hero_video" accept="video/mp4,video/webm">
                                        <small class="text-muted">Max size: 50MB. Supported formats: MP4, WEBM</small>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </form>
                            </div>
                            
                            <div class="col-md-6">
                                <h4>Preview</h4>
                                <div class="preview-box border rounded p-3">
                                    <div id="imagePreview" class="mb-3">
                                        <?php if (!empty($hero_content['hero_image'])): ?>
                                            <img src="../uploads/hero/<?php echo htmlspecialchars($hero_content['hero_image']); ?>" 
                                                 alt="Current hero image" class="img-fluid">
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div id="videoPreview" class="mb-3">
                                        <?php if (!empty($hero_content['video_url'])): ?>
                                            <video width="100%" controls>
                                                <source src="../uploads/hero/<?php echo htmlspecialchars($hero_content['video_url']); ?>" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <h2 id="headingPreview"><?php echo htmlspecialchars($hero_content['heading'] ?? ''); ?></h2>
                                    <p id="subheadingPreview"><?php echo htmlspecialchars($hero_content['subheading'] ?? ''); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Menu Section Tab -->
                    <div class="tab-pane fade" id="menu" role="tabpanel">
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="mb-0">Menu Management</h4>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-outline-secondary" onclick="toggleMenuView('list')" title="List View">
                                            <i class="bi bi-list"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary active" onclick="toggleMenuView('grid')" title="Grid View">
                                            <i class="bi bi-grid-3x3"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Left Section: Add to Menu Form -->
                            <div class="col-md-4 mb-4">
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="card-title mb-0">
                                            <i class="bi bi-plus-circle me-2"></i>Add to Menu
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="menuUploadForm" enctype="multipart/form-data" novalidate>
                                            <div class="mb-3">
                                                <label for="menuImage" class="form-label">Item Image</label>
                                                <div class="input-group">
                                                    <input type="file" class="form-control" id="menuImage" name="image" 
                                                           accept="image/jpeg,image/png,image/webp">
                                                    <button class="btn btn-outline-secondary" type="button" onclick="clearMenuImage()">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                </div>
                                                <div class="form-text">Recommended size: 800x600px (Max: 5MB)</div>
                                            </div>
                                            
                                            <div id="menuImagePreview" class="mb-3 d-none">
                                                <div class="position-relative">
                                                    <img src="" alt="Preview" class="img-fluid rounded">
                                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" 
                                                            onclick="clearMenuImage()">
                                                        <i class="bi bi-x"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="menuName" class="form-label">Item Name*</label>
                                                <input type="text" class="form-control" id="menuName" name="name" required
                                                       minlength="2" maxlength="100">
                                                <div class="invalid-feedback">Please enter an item name (2-100 characters)</div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="menuCategory" class="form-label">Category*</label>
                                                    <select class="form-control" id="menuCategory" name="category_id" required>
                                                        <?php foreach ($menu_categories as $category): ?>
                                                            <option value="<?php echo $category['id']; ?>">
                                                                <?php echo htmlspecialchars($category['name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <div class="invalid-feedback">Please select a category</div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="menuPrice" class="form-label">Price*</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">ETB</span>
                                                        <input type="number" class="form-control" id="menuPrice" name="price" 
                                                               step="0.01" min="0" required>
                                                    </div>
                                                    <div class="invalid-feedback">Please enter a valid price</div>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="menuDescription" class="form-label">Description</label>
                                                <textarea class="form-control" id="menuDescription" name="description" 
                                                          rows="3" maxlength="500"></textarea>
                                                <div class="form-text">Maximum 500 characters</div>
                                            </div>

                                            <div class="d-grid">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="bi bi-plus-circle me-2"></i>Add to Menu
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Right Section: Menu Preview -->
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="card-title mb-0">
                                                <i class="bi bi-menu-button-wide me-2"></i>Menu Items
                                            </h5>
                                            <span id="menuCount" class="badge bg-primary">0 items</span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="menuItemsContainer" class="row g-3">
                                            <!-- Menu items will be loaded here -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gallery Section Tab -->
                    <div class="tab-pane fade" id="gallery" role="tabpanel">
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="mb-0">Gallery Management</h4>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-outline-secondary" id="listViewBtn" onclick="toggleGalleryView('list')" title="List View">
                                            <i class="bi bi-list"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary active" id="gridViewBtn" onclick="toggleGalleryView('grid')" title="Grid View">
                                            <i class="bi bi-grid-3x3"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Left Section: Add to Gallery Form -->
                            <div class="col-md-4 mb-4">
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="card-title mb-0">
                                            <i class="bi bi-image-fill me-2"></i>Add to Gallery
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="galleryUploadForm" enctype="multipart/form-data" novalidate>
                                            <div class="mb-3">
                                                <label for="galleryImage" class="form-label">Select Image*</label>
                                                <div class="input-group">
                                                    <input type="file" class="form-control" id="galleryImage" name="image" 
                                                           accept="image/jpeg,image/png,image/webp" required>
                                                    <button class="btn btn-outline-secondary" type="button" onclick="document.getElementById('galleryImage').value = ''">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                </div>
                                                <div class="form-text">Accepted formats: JPG, PNG, WEBP (Max: 5MB)</div>
                                            </div>
                                            
                                            <div id="imagePreviewContainer" class="mb-3 d-none">
                                                <div class="position-relative">
                                                    <img id="imagePreview" src="" alt="Preview" class="img-fluid rounded">
                                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" 
                                                            onclick="clearImagePreview()">
                                                        <i class="bi bi-x"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="imageTitle" class="form-label">Title*</label>
                                                <input type="text" class="form-control" id="imageTitle" name="title" required
                                                       minlength="3" maxlength="100">
                                                <div class="invalid-feedback">Please enter a title (3-100 characters)</div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="imageDescription" class="form-label">Description</label>
                                                <textarea class="form-control" id="imageDescription" name="description" 
                                                          rows="3" maxlength="500"></textarea>
                                                <div class="form-text">Maximum 500 characters</div>
                                            </div>

                                            <div class="d-grid">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="bi bi-cloud-upload me-2"></i>Upload to Gallery
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Right Section: Gallery Preview -->
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="card-title mb-0">
                                                <i class="bi bi-images me-2"></i>Gallery Preview
                                            </h5>
                                            <span id="galleryCount" class="badge bg-primary">0 items</span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="galleryContainer" class="row g-3">
                                            <!-- Gallery items will be loaded here -->
                                            <div class="text-center py-5">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chefs Section Tab -->
                    <div class="tab-pane fade" id="chefs" role="tabpanel">
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="mb-0">Chef Management</h4>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-outline-secondary" onclick="sortChefs('name')" title="Sort by Name">
                                            <i class="bi bi-sort-alpha-down"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="sortChefs('date')" title="Sort by Date">
                                            <i class="bi bi-calendar-date"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Left Section: Add Chef Form -->
                            <div class="col-md-4 mb-4">
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="card-title mb-0">
                                            <i class="bi bi-person-plus-fill me-2"></i>Add New Chef
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="chefUploadForm" enctype="multipart/form-data" novalidate>
                                            <div class="mb-3">
                                                <label for="chefImage" class="form-label">Chef Photo*</label>
                                                <div class="input-group">
                                                    <input type="file" class="form-control" id="chefImage" name="image" 
                                                           accept="image/jpeg,image/png,image/webp" required>
                                                    <button class="btn btn-outline-secondary" type="button" onclick="clearChefImage()">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                </div>
                                                <div class="form-text">Recommended size: 400x400px (Max: 5MB)</div>
                                            </div>
                                            
                                            <div id="chefImagePreview" class="mb-3 d-none">
                                                <div class="position-relative">
                                                    <img src="" alt="Preview" class="img-fluid rounded">
                                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" 
                                                            onclick="clearChefImage()">
                                                        <i class="bi bi-x"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="chefName" class="form-label">Name*</label>
                                                <input type="text" class="form-control" id="chefName" name="name" required
                                                       minlength="2" maxlength="100">
                                                <div class="invalid-feedback">Please enter the chef's name (2-100 characters)</div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="chefProfession" class="form-label">Profession/Title*</label>
                                                <input type="text" class="form-control" id="chefProfession" name="profession" required
                                                       minlength="2" maxlength="100">
                                                <div class="invalid-feedback">Please enter the chef's profession</div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="chefDescription" class="form-label">Description</label>
                                                <textarea class="form-control" id="chefDescription" name="description" 
                                                          rows="3" maxlength="500"></textarea>
                                                <div class="form-text">Maximum 500 characters</div>
                                            </div>

                                            <div class="d-grid">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="bi bi-person-plus me-2"></i>Add Chef
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Right Section: Chefs Preview -->
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="card-title mb-0">
                                                <i class="bi bi-people-fill me-2"></i>Our Chefs
                                            </h5>
                                            <span id="chefCount" class="badge bg-primary">0 chefs</span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="chefsContainer" class="row g-3">
                                            <!-- Chefs will be loaded here -->
                                            <div class="text-center py-5">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- About Section Tab -->
                    <div class="tab-pane fade" id="about" role="tabpanel">
                        <div class="row">
                            <!-- Main About Section -->
                            <div class="col-md-7">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">About Section Content</h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="aboutForm" enctype="multipart/form-data">
                                            <div class="mb-3">
                                                <label for="aboutHeading" class="form-label">Heading</label>
                                                <input type="text" class="form-control" id="aboutHeading" name="heading" 
                                                       value="<?php echo htmlspecialchars($about_content['heading'] ?? ''); ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="aboutSubheading" class="form-label">Subheading</label>
                                                <input type="text" class="form-control" id="aboutSubheading" name="subheading" 
                                                       value="<?php echo htmlspecialchars($about_content['subheading'] ?? ''); ?>">
                                            </div>

                                            <div class="mb-3">
                                                <label for="aboutContent" class="form-label">Main Content</label>
                                                <textarea class="form-control" id="aboutContent" name="main_content" 
                                                        rows="6" required><?php echo htmlspecialchars($about_content['main_content'] ?? ''); ?></textarea>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="aboutMission" class="form-label">Our Mission</label>
                                                        <textarea class="form-control" id="aboutMission" name="mission" 
                                                                rows="4"><?php echo htmlspecialchars($about_content['mission'] ?? ''); ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="aboutVision" class="form-label">Our Vision</label>
                                                        <textarea class="form-control" id="aboutVision" name="vision" 
                                                                rows="4"><?php echo htmlspecialchars($about_content['vision'] ?? ''); ?></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="aboutImage" class="form-label">About Image</label>
                                                <input type="file" class="form-control" id="aboutImage" name="image" 
                                                       accept="image/jpeg,image/png,image/webp">
                                                <?php if (!empty($about_content['image'])): ?>
                                                    <div class="mt-2">
                                                        <img src="../uploads/about/<?php echo htmlspecialchars($about_content['image']); ?>" 
                                                             alt="Current about image" class="img-thumbnail" style="max-height: 150px;">
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <div class="mb-3">
                                                <label for="aboutVideo" class="form-label">Video URL (Optional)</label>
                                                <input type="url" class="form-control" id="aboutVideo" name="video_url" 
                                                       value="<?php echo htmlspecialchars($about_content['video_url'] ?? ''); ?>"
                                                       placeholder="e.g., https://www.youtube.com/watch?v=...">
                                            </div>

                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-save"></i> Save Changes
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Features Section -->
                            <div class="col-md-5">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">Features</h5>
                                        <button class="btn btn-sm btn-primary" id="addFeatureBtn">
                                            <i class="bi bi-plus-circle"></i> Add Feature
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div class="features-list">
                                            <?php foreach ($about_features as $feature): ?>
                                                <div class="feature-item" data-id="<?php echo $feature['id']; ?>">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <h6 class="mb-1">
                                                                <i class="bi bi-<?php echo htmlspecialchars($feature['icon']); ?>"></i>
                                                                <?php echo htmlspecialchars($feature['title']); ?>
                                                            </h6>
                                                            <p class="text-muted small mb-0">
                                                                <?php echo htmlspecialchars($feature['description']); ?>
                                                            </p>
                                                        </div>
                                                        <div class="feature-actions">
                                                            <button class="btn btn-sm btn-light edit-feature" 
                                                                    data-id="<?php echo $feature['id']; ?>"
                                                                    data-title="<?php echo htmlspecialchars($feature['title']); ?>"
                                                                    data-description="<?php echo htmlspecialchars($feature['description']); ?>"
                                                                    data-icon="<?php echo htmlspecialchars($feature['icon']); ?>">
                                                                <i class="bi bi-pencil"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-danger delete-feature" 
                                                                    data-id="<?php echo $feature['id']; ?>">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/vendor/dropzone/dropzone.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize gallery if we're on the gallery tab
            if (document.querySelector('#gallery.active')) {
                initializeGalleryForm();
                loadGalleryItems();
            }

            // Initialize chefs if we're on the chefs tab
            if (document.querySelector('#chefs.active')) {
                initializeChefs();
            }

            // Add tab change event listeners for Bootstrap 5
            const tabEls = document.querySelectorAll('button[data-bs-toggle="tab"]');
            tabEls.forEach(tabEl => {
                tabEl.addEventListener('shown.bs.tab', function (event) {
                    const targetId = event.target.getAttribute('data-bs-target');
                    if (targetId === '#gallery') {
                        initializeGalleryForm();
                        loadGalleryItems();
                    } else if (targetId === '#chefs') {
                        initializeChefs();
                    }
                });
            });
        });

        // Utility Functions
        function showPopupMessage(message, type = 'success') {
            const popup = document.getElementById('popupMessage');
            const icon = popup.querySelector('.bi');
            const text = popup.querySelector('.message-text');
            
            icon.className = 'bi ' + (type === 'success' ? 'bi-check-circle' : 'bi-exclamation-circle');
            text.textContent = message;
            popup.className = `popup-message show ${type}`;
            
            setTimeout(() => {
                popup.className = 'popup-message';
            }, 3000);
        }

        // Gallery Management Functions
        function initializeGalleryForm() {
            const form = document.getElementById('galleryUploadForm');
            if (!form) return;

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (!this.checkValidity()) {
                    e.stopPropagation();
                    this.classList.add('was-validated');
                    return;
                }
                uploadGalleryItem(this);
            });

            // Image preview functionality
            const imageInput = document.getElementById('galleryImage');
            if (imageInput) {
                imageInput.addEventListener('change', function() {
                    const preview = document.getElementById('imagePreviewContainer');
                    const previewImg = document.getElementById('imagePreview');
                    handleImagePreview(this, preview, previewImg);
                });
            }
        }

        function handleImagePreview(input, previewContainer, previewImg) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const maxSize = 5 * 1024 * 1024; // 5MB
                const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

                if (file.size > maxSize) {
                    showPopupMessage('Image size must be less than 5MB', 'danger');
                    input.value = '';
                    return;
                }

                if (!allowedTypes.includes(file.type)) {
                    showPopupMessage('Invalid file type. Please use JPG, PNG, or WEBP', 'danger');
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewContainer.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            } else {
                previewContainer.classList.add('d-none');
            }
        }

        function clearImagePreview() {
            const input = document.getElementById('galleryImage');
            const preview = document.getElementById('imagePreviewContainer');
            if (input) input.value = '';
            if (preview) preview.classList.add('d-none');
        }

        function uploadGalleryItem(form) {
            const formData = new FormData(form);
            formData.append('gallery_action', 'upload');
            
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Uploading...';
            submitBtn.disabled = true;

            fetch('handlers/gallery_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showPopupMessage('Image uploaded successfully!', 'success');
                    form.reset();
                    clearImagePreview();
                    loadGalleryItems();
                } else {
                    throw new Error(data.message || 'Failed to upload image');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showPopupMessage(error.message || 'An unexpected error occurred', 'danger');
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        }

        function loadGalleryItems() {
            const container = document.getElementById('galleryContainer');
            const countBadge = document.getElementById('galleryCount');
            
            if (!container) return;

            container.innerHTML = `
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;

            fetch('get_gallery_items.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.data.length === 0) {
                            container.innerHTML = `
                                <div class="col-12 text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-images display-1"></i>
                                        <p class="mt-3">No images in gallery. Add some images to get started!</p>
                                    </div>
                                </div>
                            `;
                            countBadge.textContent = '0 items';
                            return;
                        }

                        container.innerHTML = data.data.map(item => `
                            <div class="col-md-6 col-lg-4 gallery-item">
                                <div class="card h-100">
                                    <div class="card-img-container position-relative">
                                        <img src="../uploads/gallery/${item.image}" 
                                             class="card-img-top" 
                                             alt="${item.title}"
                                             style="height: 200px; object-fit: cover;">
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <button class="btn btn-sm btn-danger" onclick="deleteGalleryItem(${item.id})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title">${item.title}</h5>
                                        <p class="card-text small text-muted">${item.description || ''}</p>
                                    </div>
                                </div>
                            </div>
                        `).join('');

                        countBadge.textContent = `${data.data.length} item${data.data.length !== 1 ? 's' : ''}`;
                    } else {
                        showPopupMessage(data.message || 'Failed to load gallery items', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showPopupMessage('Failed to load gallery items', 'danger');
                });
        }

        function deleteGalleryItem(id) {
            if (!confirm('Are you sure you want to delete this image?')) {
                return;
            }

            const formData = new FormData();
            formData.append('gallery_action', 'delete');
            formData.append('id', id);

            fetch('handlers/gallery_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showPopupMessage('Image deleted successfully!', 'success');
                    loadGalleryItems();
                } else {
                    showPopupMessage(data.message || 'Failed to delete image', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showPopupMessage('An unexpected error occurred', 'danger');
            });
        }

        function toggleGalleryView(view) {
            const container = document.getElementById('galleryContainer');
            const listBtn = document.getElementById('listViewBtn');
            const gridBtn = document.getElementById('gridViewBtn');
            
            if (view === 'list') {
                container.classList.add('list-view');
                listBtn.classList.add('active');
                gridBtn.classList.remove('active');
            } else {
                container.classList.remove('list-view');
                gridBtn.classList.add('active');
                listBtn.classList.remove('active');
            }
        }

        // Chefs Management Functions
        function initializeChefs() {
            loadChefs();
            initializeChefForm();
        }

        function loadChefs() {
            const container = document.getElementById('chefsContainer');
            const countBadge = document.getElementById('chefCount');
            
            if (!container) {
                console.error('Chefs container not found');
                return;
            }

            console.log('Starting to load chefs...'); // Debug log
            
            container.innerHTML = `
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;

            const url = 'get_chefs.php?t=' + new Date().getTime();
            console.log('Fetching from URL:', url); // Debug log

            fetch(url)
                .then(response => {
                    console.log('Response status:', response.status); // Debug log
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Received data:', data); // Debug log
                    
                    if (data.success) {
                        if (!Array.isArray(data.data) || data.data.length === 0) {
                            container.innerHTML = `
                                <div class="col-12 text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-people display-1"></i>
                                        <p class="mt-3">No chefs found. Add some chefs to get started!</p>
                                    </div>
                                </div>
                            `;
                            countBadge.textContent = '0 chefs';
                            return;
                        }

                        container.innerHTML = data.data.map(chef => `
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card chef-card h-100">
                                    <div class="card-img-container">
                                        <img src="../uploads/chefs/${chef.image}" 
                                                 class="card-img-top" 
                                             alt="${chef.name}"
                                             style="height: 300px; object-fit: cover;">
                                        <div class="action-buttons">
                                            <button class="btn btn-light btn-sm" 
                                                    onclick="editChef({
                                                        id: ${chef.id},
                                                        name: '${chef.name.replace(/'/g, "\\'")}',
                                                        profession: '${chef.profession.replace(/'/g, "\\'")}',
                                                        description: '${(chef.description || '').replace(/'/g, "\\'")}',
                                                        image: '${chef.image}'
                                                    })"
                                                    title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm" 
                                                    onclick="deleteChef(${chef.id})"
                                                    title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title">${chef.name}</h5>
                                        <p class="card-text profession">${chef.profession}</p>
                                        <p class="card-text small text-muted">${chef.description || ''}</p>
                                    </div>
                                </div>
                            </div>
                        `).join('');

                        countBadge.textContent = `${data.data.length} chef${data.data.length !== 1 ? 's' : ''}`;
                    } else {
                        throw new Error(data.message || 'Failed to load chefs');
                    }
                })
                .catch(error => {
                    console.error('Error loading chefs:', error);
                    container.innerHTML = `
                        <div class="col-12">
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle me-2"></i>${error.message || 'Error loading chefs'}
                            </div>
                        </div>
                    `;
                });
        }

        function editChef(chef) {
            // Implementation for editing chef
            console.log('Editing chef:', chef);
        }

        function deleteChef(id) {
            // Implementation for deleting chef
            console.log('Deleting chef:', id);
        }

        function sortChefs(type) {
            const container = document.getElementById('chefsContainer');
            const cards = Array.from(container.children);
            
            cards.sort((a, b) => {
                if (type === 'name') {
                    const nameA = a.querySelector('.card-title').textContent.toLowerCase();
                    const nameB = b.querySelector('.card-title').textContent.toLowerCase();
                    return nameA.localeCompare(nameB);
                } else if (type === 'date') {
                    const dateA = new Date(a.dataset.created);
                    const dateB = new Date(b.dataset.created);
                    return dateB - dateA;
                }
                return 0;
            });
            
            container.innerHTML = '';
            cards.forEach(card => container.appendChild(card));
        }

        // Menu Management Functions
        function initializeMenuForm() {
            const form = document.getElementById('menuUploadForm');
            if (!form) {
                console.error('Menu form not found');
                return;
            }

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (!this.checkValidity()) {
                    e.stopPropagation();
                    this.classList.add('was-validated');
                    return;
                }

                const formData = new FormData(this);
                formData.append('action', 'add');

                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving...';
                submitBtn.disabled = true;

                fetch('handlers/menu_handler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showPopupMessage('Menu item added successfully!', 'success');
                        this.reset();
                        clearMenuImage();
                        loadMenuItems();
                    } else {
                        throw new Error(data.message || 'Failed to add menu item');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showPopupMessage(error.message || 'An unexpected error occurred', 'danger');
                })
                .finally(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
            });

            // Image preview functionality
            const imageInput = document.getElementById('menuImage');
            if (imageInput) {
                imageInput.addEventListener('change', function() {
                    const preview = document.getElementById('menuImagePreview');
                    if (preview) {
                        handleImagePreview(this, preview.parentElement, preview);
                    }
                });
            }
        }

        function loadMenuItems() {
            const container = document.getElementById('menuItemsContainer');
            const countBadge = document.getElementById('menuCount');
            
            if (!container) {
                console.error('Menu container not found');
                return;
            }

            container.innerHTML = `
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;

            fetch('get_menu_items.php')
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        if (!data.data || data.data.length === 0) {
                            container.innerHTML = `
                                <div class="col-12 text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-menu-button-wide display-1"></i>
                                        <p class="mt-3">No menu items found. Add some items to get started!</p>
                                    </div>
                                </div>
                            `;
                            countBadge.textContent = '0 items';
                            return;
                        }

                        container.innerHTML = data.data.map(item => `
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100 menu-item">
                                    <div class="card-img-container position-relative">
                                        ${item.image ? `
                                            <img src="../uploads/menu/${item.image}" 
                                                 class="card-img-top" 
                                                 alt="${item.name}"
                                                 style="height: 200px; object-fit: cover;">
                                        ` : `
                                            <div class="no-image-placeholder" style="height: 200px;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        `}
                                        <div class="action-buttons position-absolute top-0 end-0 m-2">
                                            <button class="btn btn-sm btn-light me-1" onclick="editMenuItem(${item.id})">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteMenuItem(${item.id})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                        <span class="badge bg-primary position-absolute bottom-0 start-0 m-2">
                                            ETB ${parseFloat(item.price).toFixed(2)}
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="card-title mb-0">${item.name}</h5>
                                            <span class="badge bg-secondary">${item.category_name}</span>
                                        </div>
                                        <p class="card-text small text-muted">${item.description || ''}</p>
                                    </div>
                                </div>
                            </div>
                        `).join('');

                        countBadge.textContent = `${data.data.length} item${data.data.length !== 1 ? 's' : ''}`;
                    } else {
                        throw new Error(data.message || 'Failed to load menu items');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    container.innerHTML = `
                        <div class="col-12">
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle me-2"></i>${error.message || 'Failed to load menu items'}
                            </div>
                        </div>
                    `;
                });
        }

        // Initialize menu management when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            if (document.querySelector('#menu.active')) {
                initializeMenuForm();
                loadMenuItems();
            }

            // Add tab change event listener
            const tabEls = document.querySelectorAll('button[data-bs-toggle="tab"]');
            tabEls.forEach(tabEl => {
                tabEl.addEventListener('shown.bs.tab', function (event) {
                    const targetId = event.target.getAttribute('data-bs-target');
                    if (targetId === '#menu') {
                        initializeMenuForm();
                        loadMenuItems();
                    }
                });
            });
        });
    </script>

    <!-- Add this before </body> -->
    <div id="popupMessage" class="popup-message">
        <i class="bi"></i>
        <span class="message-text"></span>
    </div>

    <!-- Image Preview Modal -->
    <div class="modal fade" id="imageViewModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <img src="" alt="Preview" class="img-fluid w-100">
                </div>
                <div class="modal-footer">
                    <small class="text-muted me-auto" id="imageDescription"></small>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Chef Modal -->
    <div class="modal fade" id="editChefModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Chef</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editChefForm" enctype="multipart/form-data">
                        <input type="hidden" id="editChefId" name="id">
                        <div class="mb-3">
                            <label for="editChefImage" class="form-label">Chef Photo</label>
                            <input type="file" class="form-control" id="editChefImage" name="image" 
                                   accept="image/jpeg,image/png,image/webp">
                            <div class="form-text">Leave empty to keep current image</div>
                        </div>
                        
                        <div id="editChefPreviewContainer" class="mb-3">
                            <img id="editChefPreview" src="" alt="Preview" class="img-fluid rounded">
                        </div>

                        <div class="mb-3">
                            <label for="editChefName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="editChefName" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editChefProfession" class="form-label">Profession</label>
                            <input type="text" class="form-control" id="editChefProfession" name="profession" required>
                        </div>

                        <div class="mb-3">
                            <label for="editChefDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="editChefDescription" name="description" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveChefEdit">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Feature Modal -->
    <div class="modal fade" id="featureModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add/Edit Feature</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="featureForm">
                        <input type="hidden" id="featureId" name="id">
                        <div class="mb-3">
                            <label for="featureTitle" class="form-label">Title</label>
                            <input type="text" class="form-control" id="featureTitle" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="featureDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="featureDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="featureIcon" class="form-label">Icon (Bootstrap Icons class name)</label>
                            <input type="text" class="form-control" id="featureIcon" name="icon" 
                                   placeholder="e.g., check-circle, star, award">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveFeature">Save Feature</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Modal -->
    <div class="modal fade" id="menuModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Menu Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="menuForm" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="">
                        <div class="mb-3">
                            <label for="menuName" class="form-label">Name*</label>
                            <input type="text" class="form-control" id="menuName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="menuPrice" class="form-label">Price (ETB)*</label>
                            <div class="input-group">
                                <span class="input-group-text">ETB</span>
                                <input type="number" class="form-control" id="menuPrice" name="price" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="menuCategory" class="form-label">Category*</label>
                            <select class="form-control" id="menuCategory" name="category_id" required>
                                <?php foreach ($menu_categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="menuDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="menuDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="menuImage" class="form-label">Image</label>
                            <input type="file" class="form-control" id="menuImage" name="image" accept="image/*">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveMenuItem()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 