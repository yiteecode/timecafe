<?php
session_start();
require_once '../../db-config.php';
require_once '../includes/auth_check.php';

// Check authentication
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

// Include header
include '../includes/header.php';

// Get about section data
$about_query = "SELECT * FROM about_section LIMIT 1";
$about_result = mysqli_query($connect, $about_query);
$about_data = mysqli_fetch_assoc($about_result);

// Get features/points
$features_query = "SELECT * FROM about_features ORDER BY sort_order";
$features_result = mysqli_query($connect, $features_query);
?>

<div class="container-fluid p-0">
    <div class="row g-0">
        <div class="col-12">
            <h1 class="mb-4">About Section Management</h1>
            
            <!-- Main Content Section -->
            <div class="about-section">
                <h4 class="mb-4">Main Content</h4>
                <form id="aboutForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="heading" class="form-label">Heading*</label>
                            <input type="text" class="form-control" id="heading" name="heading" 
                                   value="<?php echo htmlspecialchars($about_data['heading'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="subheading" class="form-label">Subheading</label>
                            <input type="text" class="form-control" id="subheading" name="subheading"
                                   value="<?php echo htmlspecialchars($about_data['subheading'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?php 
                            echo htmlspecialchars($about_data['description'] ?? ''); 
                        ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Save Changes
                    </button>
                </form>
            </div>

            <!-- Features Section -->
            <div class="about-features">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">Features/Points</h4>
                    <button class="btn btn-primary btn-add-feature" onclick="addFeature()">
                        <i class="bi bi-plus-circle"></i> Add Feature
                    </button>
                </div>

                <div id="featuresContainer">
                    <?php while ($feature = mysqli_fetch_assoc($features_result)): ?>
                        <div class="feature-item" data-id="<?php echo $feature['id']; ?>">
                            <div class="feature-header">
                                <h5 class="mb-0"><?php echo htmlspecialchars($feature['title']); ?></h5>
                                <div class="feature-actions">
                                    <button class="btn btn-sm btn-outline-primary" onclick="editFeature(<?php echo $feature['id']; ?>)">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteFeature(<?php echo $feature['id']; ?>)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <p class="mb-0"><?php echo htmlspecialchars($feature['description']); ?></p>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include About Modals -->
<?php include '../includes/modals/about_modals.php'; ?>

<!-- About Scripts -->
<script src="../js/about.js"></script>

<?php include '../includes/footer.php'; ?>
