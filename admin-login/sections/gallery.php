<?php
// Check if user is logged in
session_start();
require_once '../../db-config.php';

// Check authentication
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

// Get gallery items
$query = "SELECT * FROM gallery WHERE active = 1 ORDER BY sort_order ASC";
$gallery = mysqli_query($connect, $query);

$isSection = true;
include '../includes/header.php';
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Gallery Management</h1>
            <p class="text-muted">Add and manage your gallery images</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary" onclick="toggleView()">
                <i class="bi bi-grid"></i> Toggle View
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Add Gallery Item Form -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Add Gallery Item</h5>
                </div>
                <div class="card-body">
                    <form id="galleryForm" enctype="multipart/form-data" novalidate>
                        <!-- Image Upload -->
                        <div class="mb-3">
                            <label for="galleryImage" class="form-label">Image*</label>
                            <input type="file" class="form-control" id="galleryImage" name="image" 
                                   accept="image/jpeg,image/png,image/webp" required>
                            <div class="form-text">Accepted formats: JPG, PNG, WEBP (Max: 5MB)</div>
                        </div>

                        <!-- Image Preview -->
                        <div id="imagePreviewContainer" class="mb-3 d-none">
                            <!-- Preview will be inserted here via JavaScript -->
                        </div>

                        <!-- Title -->
                        <div class="mb-3">
                            <label for="galleryTitle" class="form-label">Title*</label>
                            <input type="text" class="form-control" id="galleryTitle" name="title" 
                                   required minlength="3" maxlength="100">
                            <div class="invalid-feedback">Please enter a title (3-100 characters)</div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="galleryDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="galleryDescription" name="description" 
                                      rows="3" maxlength="500"></textarea>
                            <div class="form-text">Brief description of the image (optional)</div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Add to Gallery
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Gallery Items Preview -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Gallery Items</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-4" id="galleryItemsGrid">
                        <?php if (mysqli_num_rows($gallery) > 0): ?>
                            <?php while ($item = mysqli_fetch_assoc($gallery)): ?>
                                <?php include '../includes/gallery_item_card.php'; ?>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="col-12 text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-images display-1"></i>
                                    <p class="mt-3">No gallery items found. Add your first image to get started!</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Gallery Modals -->
<?php include '../includes/modals/gallery_modals.php'; ?>

<!-- Include Sortable.js for drag-and-drop functionality -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<!-- Gallery Scripts -->
<script src="../js/gallery.js"></script>

<?php include '../includes/footer.php'; ?> 