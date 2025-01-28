<?php
session_start();
require_once '../../db-config.php';
require_once '../includes/auth_check.php';

// Get hero section data
$query = "SELECT * FROM hero_section LIMIT 1";
$result = mysqli_query($connect, $query);
$hero_data = mysqli_fetch_assoc($result);

$isSection = true;
include '../includes/header.php';
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="card-title mb-1">Hero Section Management</h2>
                            <p class="mb-0">Customize your website's first impression</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Hero Content Form -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">Hero Content</h5>
                </div>
                <div class="card-body">
                    <form id="heroForm" enctype="multipart/form-data">
                        <!-- Media Section -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Media Content</label>
                            <div class="row g-3">
                                <!-- Background Image -->
                                <div class="col-md-6">
                                    <label class="form-label">Background Image</label>
                                    <input type="file" class="form-control" id="heroImage" name="image" 
                                           accept="image/jpeg,image/png,image/webp">
                                    <div class="form-text">Recommended: 1920x1080px (16:9)</div>
                                    <?php if (!empty($hero_data['hero_image'])): ?>
                                        <div class="mt-2">
                                            <small class="text-muted">Current: <?php echo htmlspecialchars($hero_data['hero_image']); ?></small>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Background Video -->
                                <div class="col-md-6">
                                    <label class="form-label">Background Video</label>
                                    <input type="file" class="form-control" id="heroVideo" name="video" 
                                           accept="video/mp4,video/webm">
                                    <div class="form-text">Max size: 10MB, Format: MP4/WebM</div>
                                    <?php if (!empty($hero_data['video_url'])): ?>
                                        <div class="mt-2">
                                            <small class="text-muted">Current: <?php echo htmlspecialchars($hero_data['video_url']); ?></small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Text Content -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Text Content</label>
                            <!-- Heading -->
                            <div class="mb-3">
                                <label for="heading" class="form-label">Main Heading</label>
                                <input type="text" class="form-control" id="heading" name="heading" 
                                       value="<?php echo htmlspecialchars($hero_data['heading'] ?? ''); ?>" required>
                            </div>

                            <!-- Subheading -->
                            <div class="mb-3">
                                <label for="subheading" class="form-label">Subheading</label>
                                <textarea class="form-control" id="subheading" name="subheading" rows="2"><?php 
                                    echo htmlspecialchars($hero_data['subheading'] ?? ''); 
                                ?></textarea>
                            </div>
                        </div>

                        <!-- Save Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-light">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Preview Card -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h3 class="card-title mb-0">Preview</h3>
                </div>
                <div class="card-body p-0">
                    <div class="preview-container">
                        <!-- Media Preview -->
                        <div class="preview-box">
                            <?php if (!empty($hero_data['hero_image'])): ?>
                                <div class="preview-image-container">
                                    <img src="../../uploads/hero/<?php echo htmlspecialchars($hero_data['hero_image']); ?>" 
                                         alt="Hero preview" class="preview-media">
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($hero_data['video_url'])): ?>
                                <div class="preview-video-container">
                                    <video class="preview-media" controls>
                                        <source src="../../uploads/hero/<?php echo htmlspecialchars($hero_data['video_url']); ?>" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                            <?php endif; ?>

                            <?php if (empty($hero_data['hero_image']) && empty($hero_data['video_url'])): ?>
                                <div class="placeholder-media">
                                    <i class="bi bi-image"></i>
                                    <p>No media uploaded</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Text Preview -->
                        <div class="text-preview">
                            <h3 class="preview-heading">
                                <?php echo htmlspecialchars($hero_data['heading'] ?? 'Your Heading Here'); ?>
                            </h3>
                            <p class="preview-subheading mb-0">
                                <?php echo nl2br(htmlspecialchars($hero_data['subheading'] ?? 'Your subheading text will appear here')); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.preview-container {
    background: #fff;
    border-radius: 0.25rem;
    overflow: hidden;
}

.preview-box {
    padding: 1rem;
    background: #fff;
}

.preview-image-container,
.preview-video-container {
    width: 100%;
    border-radius: 0.5rem;
    overflow: hidden;
    margin-bottom: 1rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.preview-media {
    width: 100%;
    height: auto;
    display: block;
    object-fit: cover;
}

.placeholder-media {
    height: 200px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    background: #f8f9fa;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
}

.placeholder-media i {
    font-size: 3rem;
    margin-bottom: 0.5rem;
}

.text-preview {
    padding: 1rem;
    background: white;
}

.preview-heading {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #2c3e50;
}

.preview-subheading {
    font-size: 0.9rem;
    color: #6c757d;
    line-height: 1.4;
}

.card-title {
    font-size: 1.5rem;
    font-weight: normal;
    color: #333;
}
</style>

<!-- Include Hero Scripts -->
<script src="../js/hero.js"></script>

<?php include '../includes/footer.php'; ?>
