<?php
session_start();
require_once '../../db-config.php';

// Check authentication
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

// Get chefs
$query = "SELECT * FROM chefs WHERE active = 1 ORDER BY sort_order ASC";
$chefs = mysqli_query($connect, $query);

$isSection = true;
include '../includes/header.php';
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Chefs Management</h1>
            <p class="text-muted">Add and manage your culinary team</p>
        </div>
    </div>

    <div class="row">
        <!-- Add Chef Form -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Add Chef</h5>
                </div>
                <div class="card-body">
                    <form id="chefForm" enctype="multipart/form-data" novalidate>
                        <!-- Image Upload -->
                        <div class="mb-3">
                            <label for="chefImage" class="form-label">Chef's Photo*</label>
                            <input type="file" class="form-control" id="chefImage" name="image" 
                                   accept="image/jpeg,image/png,image/webp" required>
                            <div class="form-text">Professional photo (Max: 2MB)</div>
                        </div>

                        <!-- Image Preview -->
                        <div id="imagePreviewContainer" class="mb-3 d-none">
                            <!-- Preview will be inserted here via JavaScript -->
                        </div>

                        <!-- Name -->
                        <div class="mb-3">
                            <label for="chefName" class="form-label">Full Name*</label>
                            <input type="text" class="form-control" id="chefName" name="name" 
                                   required minlength="3" maxlength="100">
                            <div class="invalid-feedback">Please enter the chef's full name</div>
                        </div>

                        <!-- Profession/Title -->
                        <div class="mb-3">
                            <label for="chefProfession" class="form-label">Profession*</label>
                            <input type="text" class="form-control" id="chefProfession" name="profession" 
                                   required maxlength="50">
                            <div class="form-text">e.g., Executive Chef, Pastry Chef, etc.</div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="chefDescription" class="form-label">Biography</label>
                            <textarea class="form-control" id="chefDescription" name="description" 
                                      rows="4" maxlength="500"></textarea>
                            <div class="form-text">Brief description of experience and specialties</div>
                        </div>

                        <!-- Social Media Links -->
                        <div class="mb-3">
                            <label class="form-label">Social Media (Optional)</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text"><i class="bi bi-facebook"></i></span>
                                <input type="url" class="form-control" name="facebook" placeholder="Facebook Profile URL">
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text"><i class="bi bi-instagram"></i></span>
                                <input type="url" class="form-control" name="instagram" placeholder="Instagram Profile URL">
                            </div>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-twitter"></i></span>
                                <input type="url" class="form-control" name="twitter" placeholder="Twitter Profile URL">
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Add Chef
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Chefs Preview -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Current Team</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4" id="chefsGrid">
                        <?php if (mysqli_num_rows($chefs) > 0): ?>
                            <?php while ($chef = mysqli_fetch_assoc($chefs)): ?>
                                <div class="col-md-6 chef-item" data-id="<?php echo $chef['id']; ?>">
                                    <div class="card h-100">
                                        <div class="card-img-top position-relative chef-image">
                                            <img src="../../uploads/chefs/<?php echo htmlspecialchars($chef['image']); ?>" 
                                                 class="img-fluid" alt="<?php echo htmlspecialchars($chef['name']); ?>">
                                            <div class="sort-handle position-absolute top-0 start-0 m-2">
                                                <i class="bi bi-grip-vertical text-white"></i>
                                            </div>
                                        </div>
                                        <div class="card-body text-center">
                                            <h5 class="card-title mb-1"><?php echo htmlspecialchars($chef['name']); ?></h5>
                                            <p class="text-muted mb-3"><?php echo htmlspecialchars($chef['profession']); ?></p>
                                            <?php if (!empty($chef['description'])): ?>
                                                <p class="card-text small">
                                                    <?php echo htmlspecialchars($chef['description']); ?>
                                                </p>
                                            <?php endif; ?>
                                            
                                            <!-- Social Media Links -->
                                            <?php if (!empty($chef['facebook']) || !empty($chef['instagram']) || !empty($chef['twitter'])): ?>
                                                <div class="social-links mt-3">
                                                    <?php if (!empty($chef['facebook'])): ?>
                                                        <a href="<?php echo htmlspecialchars($chef['facebook']); ?>" 
                                                           class="btn btn-sm btn-outline-primary" target="_blank">
                                                            <i class="bi bi-facebook"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if (!empty($chef['instagram'])): ?>
                                                        <a href="<?php echo htmlspecialchars($chef['instagram']); ?>" 
                                                           class="btn btn-sm btn-outline-danger" target="_blank">
                                                            <i class="bi bi-instagram"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if (!empty($chef['twitter'])): ?>
                                                        <a href="<?php echo htmlspecialchars($chef['twitter']); ?>" 
                                                           class="btn btn-sm btn-outline-info" target="_blank">
                                                            <i class="bi bi-twitter"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <div class="d-flex justify-content-end gap-2">
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        onclick="editChef(<?php echo $chef['id']; ?>)">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="deleteChef(<?php echo $chef['id']; ?>)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="col-12 text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-people display-1"></i>
                                    <p class="mt-3">No chefs added yet. Add your first team member to get started!</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Chef Modal -->
<div class="modal fade" id="editChefModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Chef</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editChefForm" enctype="multipart/form-data" novalidate>
                <div class="modal-body">
                    <input type="hidden" id="editChefId" name="id">
                    
                    <!-- Name -->
                    <div class="mb-3">
                        <label for="editChefName" class="form-label">Full Name*</label>
                        <input type="text" class="form-control" id="editChefName" name="name" required>
                    </div>

                    <!-- Profession -->
                    <div class="mb-3">
                        <label for="editChefProfession" class="form-label">Profession*</label>
                        <input type="text" class="form-control" id="editChefProfession" name="profession" required>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="editChefDescription" class="form-label">Biography</label>
                        <textarea class="form-control" id="editChefDescription" name="description" rows="4"></textarea>
                    </div>

                    <!-- Image -->
                    <div class="mb-3">
                        <label for="editChefImage" class="form-label">New Photo</label>
                        <input type="file" class="form-control" id="editChefImage" name="image" 
                               accept="image/jpeg,image/png,image/webp">
                        <div class="form-text">Leave empty to keep current image</div>
                    </div>

                    <!-- Social Media -->
                    <div class="mb-3">
                        <label class="form-label">Social Media (Optional)</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="bi bi-facebook"></i></span>
                            <input type="url" class="form-control" id="editChefFacebook" name="facebook" placeholder="Facebook Profile URL">
                        </div>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="bi bi-instagram"></i></span>
                            <input type="url" class="form-control" id="editChefInstagram" name="instagram" placeholder="Instagram Profile URL">
                        </div>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-twitter"></i></span>
                            <input type="url" class="form-control" id="editChefTwitter" name="twitter" placeholder="Twitter Profile URL">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Include Sortable.js for drag-and-drop functionality -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<!-- Chef Scripts -->
<script src="../js/chefs.js"></script>

<?php include '../includes/footer.php'; ?>
