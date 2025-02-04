<?php
session_start();
require_once '../../db-config.php';
require_once '../includes/auth_check.php';

// Set section flag
$isSection = true;

// Handle logo upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['brand_logo'])) {
    $uploadDir = '../../assets/img/';
    $fileExtension = strtolower(pathinfo($_FILES['brand_logo']['name'], PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    
    if (in_array($fileExtension, $allowedExtensions)) {
        // Generate unique filename
        $newFileName = 'logo_' . time() . '.' . $fileExtension;
        $uploadFile = $uploadDir . $newFileName;
        
        if (move_uploaded_file($_FILES['brand_logo']['tmp_name'], $uploadFile)) {
            // Delete old logo if exists
            $stmt = $connect->prepare("SELECT setting_value FROM settings WHERE setting_key = 'brand_logo'");
            $stmt->execute();
            $oldLogo = $stmt->get_result()->fetch_assoc()['setting_value'];
            
            if ($oldLogo && $oldLogo !== 'time-logo.png' && file_exists($uploadDir . $oldLogo)) {
                unlink($uploadDir . $oldLogo);
            }

            // Update database with new logo filename
            $stmt = $connect->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'brand_logo'");
            $stmt->bind_param("s", $newFileName);
            
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Brand logo updated successfully!";
            } else {
                $_SESSION['error_message'] = "Failed to update database.";
            }
        } else {
            $_SESSION['error_message'] = "Failed to upload the logo.";
        }
    } else {
        $_SESSION['error_message'] = "Invalid file type. Please upload a JPG, JPEG, PNG, or GIF file.";
    }
    
    header("Location: settings.php");
    exit();
}

// Get current logo
$stmt = $connect->prepare("SELECT setting_value FROM settings WHERE setting_key = 'brand_logo'");
$stmt->execute();
$result = $stmt->get_result();
$currentLogo = $result->fetch_assoc()['setting_value'] ?? 'time-logo.png';

// Set page title
$pageTitle = "Settings";
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">System Settings</h5>
                </div>
                <div class="card-body">
                    <!-- Success/Error Messages -->
                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php 
                            echo $_SESSION['success_message'];
                            unset($_SESSION['success_message']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php 
                            echo $_SESSION['error_message'];
                            unset($_SESSION['error_message']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Brand Logo Section -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="settings-card">
                                <h6 class="mb-3">Brand Logo</h6>
                                <div class="current-logo mb-3">
                                    <p class="text-muted mb-2">Current Logo:</p>
                                    <img src="../../assets/img/<?php echo htmlspecialchars($currentLogo); ?>?v=<?php echo time(); ?>" 
                                         alt="Current Brand Logo" 
                                         class="preview-logo"
                                         id="logoPreview">
                                </div>
                                <form action="" method="POST" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label for="brand_logo" class="form-label">Upload New Logo</label>
                                        <div class="input-group">
                                            <input type="file" class="form-control" id="brand_logo" name="brand_logo" accept="image/*" required>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-upload me-2"></i>Change Now
                                            </button>
                                        </div>
                                        <div class="form-text">Recommended size: 200x100 pixels. Supported formats: JPG, JPEG, PNG, GIF</div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Preview image before upload
document.getElementById('brand_logo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('logoPreview').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});
</script>

<?php include '../includes/footer.php'; ?> 