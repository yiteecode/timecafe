<?php
session_start();
require_once '../../db-config.php';

// Check authentication
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

// Get menu categories
$categories = ['Breakfast', 'Lunch', 'Dinner', 'Beverages'];

// Get menu items
$query = "SELECT * FROM menu_items WHERE active = 1 ORDER BY category_id, sort_order ASC";
$menu_items = mysqli_query($connect, $query);

$isSection = true;
include '../includes/header.php';
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Menu Management</h1>
            <p class="text-muted">Add and manage your menu items</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary" onclick="toggleView()">
                <i class="bi bi-grid"></i> Toggle View
            </button>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
                <i class="bi bi-folder-plus"></i> Manage Categories
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Add Menu Item Form -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Add Menu Item</h5>
                </div>
                <div class="card-body">
                    <form id="menuItemForm" enctype="multipart/form-data" novalidate>
                        <!-- Image Upload -->
                        <div class="mb-3">
                            <label for="menuImage" class="form-label">Item Image*</label>
                            <input type="file" class="form-control" id="menuImage" name="image" 
                                   accept="image/jpeg,image/png,image/webp" required>
                            <div class="form-text">Accepted formats: JPG, PNG, WEBP (Max: 2MB)</div>
                        </div>

                        <!-- Image Preview -->
                        <div id="imagePreviewContainer" class="mb-3 d-none">
                            <!-- Preview will be inserted here via JavaScript -->
                        </div>

                        <!-- Item Name -->
                        <div class="mb-3">
                            <label for="menuName" class="form-label">Item Name*</label>
                            <input type="text" class="form-control" id="menuName" name="name" 
                                   required minlength="3" maxlength="255">
                        </div>

                        <!-- Category -->
                        <div class="mb-3">
                            <label for="menuCategory" class="form-label">Category*</label>
                            <select class="form-select" id="menuCategory" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $index => $category): ?>
                                    <option value="<?php echo $index + 1; ?>">
                                        <?php echo htmlspecialchars($category); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Price -->
                        <div class="mb-3">
                            <label for="menuPrice" class="form-label">Price*</label>
                            <div class="input-group">
                                <span class="input-group-text">ETB</span>
                                <input type="number" class="form-control" id="menuPrice" name="price" 
                                       step="0.01" min="0" required>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="menuDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="menuDescription" name="description" 
                                      rows="3" maxlength="1000"></textarea>
                            <div class="form-text">Describe ingredients, preparation, etc.</div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Add Menu Item
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Menu Items Preview -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Menu Items</h5>
                        <div class="d-flex gap-2 align-items-center">
                            <select class="form-select form-select-sm" onchange="filterByCategory(this.value)">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $index => $category): ?>
                                    <option value="<?php echo $index + 1; ?>">
                                        <?php echo htmlspecialchars($category); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-4" id="menuItemsGrid">
                        <?php if (mysqli_num_rows($menu_items) > 0): ?>
                            <?php while ($item = mysqli_fetch_assoc($menu_items)): ?>
                                <div class="col-md-6 menu-item" data-category="<?php echo $item['category_id']; ?>">
                                    <div class="card h-100">
                                        <div class="card-img-top position-relative">
                                            <img src="../../uploads/menu/<?php echo htmlspecialchars($item['image']); ?>" 
                                                 class="img-fluid" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                            <div class="sort-handle position-absolute top-0 start-0 m-2">
                                                <i class="bi bi-grip-vertical text-white"></i>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title d-flex justify-content-between">
                                                <?php echo htmlspecialchars($item['name']); ?>
                                                <span class="text-primary">ETB <?php echo number_format($item['price'], 2); ?></span>
                                            </h5>
                                            <?php if (!empty($item['description'])): ?>
                                                <p class="card-text small">
                                                    <?php echo htmlspecialchars($item['description']); ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <div class="d-flex justify-content-end gap-2">
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        onclick="editMenuItem(<?php echo $item['id']; ?>)">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="deleteMenuItem(<?php echo $item['id']; ?>)">
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
                                    <i class="bi bi-cup-straw display-1"></i>
                                    <p class="mt-3">No menu items found. Add your first item to get started!</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Menu Modals -->
<?php include '../includes/modals/menu_modals.php'; ?>

<!-- Menu Scripts -->
<script src="../js/menu.js"></script>

<?php include '../includes/footer.php'; ?>
