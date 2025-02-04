<?php
session_start();
require_once '../../db-config.php';
require_once '../includes/auth_check.php';

// Check authentication
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

// Get about section data
$about_query = "SELECT * FROM about_section LIMIT 1";
$about_result = mysqli_query($connect, $about_query);
$about_data = mysqli_fetch_assoc($about_result);

// Get features/points
$features_query = "SELECT * FROM about_features ORDER BY sort_order";
$features_result = mysqli_query($connect, $features_query);

$isSection = true;
include '../includes/header.php';
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">About Section Management</h1>
            <p class="text-muted">Manage your about section content</p>
        </div>
    </div>

    <div class="row">
        <!-- Main Content Form -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Main Content</h5>
                </div>
                <div class="card-body">
                    <form id="aboutForm" enctype="multipart/form-data">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="heading" class="form-label">Heading*</label>
                                <input type="text" class="form-control" id="heading" name="heading" 
                                       value="<?php echo htmlspecialchars($about_data['heading'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="subheading" class="form-label">Subheading</label>
                                <input type="text" class="form-control" id="subheading" name="subheading"
                                       value="<?php echo htmlspecialchars($about_data['subheading'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="main_content" class="form-label">Main Content*</label>
                            <textarea class="form-control" id="main_content" name="main_content" 
                                    rows="4" required><?php echo htmlspecialchars($about_data['main_content'] ?? ''); ?></textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="mission" class="form-label">Mission</label>
                                <textarea class="form-control" id="mission" name="mission" 
                                        rows="3"><?php echo htmlspecialchars($about_data['mission'] ?? ''); ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="vision" class="form-label">Vision</label>
                                <textarea class="form-control" id="vision" name="vision" 
                                        rows="3"><?php echo htmlspecialchars($about_data['vision'] ?? ''); ?></textarea>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="aboutImage" class="form-label">About Image</label>
                            <input type="file" class="form-control" id="aboutImage" name="image" 
                                   accept="image/jpeg,image/png,image/webp">
                            <?php if (!empty($about_data['image'])): ?>
                                <div class="mt-2">
                                    <img src="../../uploads/about/<?php echo htmlspecialchars($about_data['image']); ?>" 
                                         class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="videoUrl" class="form-label">Video URL</label>
                            <input type="url" class="form-control" id="videoUrl" name="video_url"
                                   value="<?php echo htmlspecialchars($about_data['video_url'] ?? ''); ?>"
                                   placeholder="YouTube video URL">
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Features</h5>
                    <button class="btn btn-primary btn-sm" onclick="addFeature()">
                        <i class="bi bi-plus-circle"></i> Add Feature
                    </button>
                </div>
                <div class="card-body">
                    <div class="features-list">
                        <?php while ($feature = mysqli_fetch_assoc($features_result)): ?>
                            <div class="feature-item card mb-3" data-id="<?php echo $feature['id']; ?>">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="d-flex gap-2">
                                            <?php 
                                            // Clean and format the icon class
                                            $icon = trim($feature['icon']);
                                            // Remove 'bi-' or 'bi ' prefix if present
                                            $icon = preg_replace('/^(bi-|bi\s+)/', '', $icon);
                                            // Add proper bi class
                                            $icon = 'bi-' . $icon;
                                            ?>
                                            <i class="bi <?php echo htmlspecialchars($icon); ?> fs-4 text-primary"></i>
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($feature['title']); ?></h6>
                                                <p class="mb-0 small text-muted">
                                                    <?php echo htmlspecialchars($feature['description']); ?>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    onclick="editFeature(<?php echo $feature['id']; ?>)">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteFeature(<?php echo $feature['id']; ?>)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
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
