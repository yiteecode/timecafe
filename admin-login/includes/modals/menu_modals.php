<!-- Edit Menu Item Modal -->
<div class="modal fade" id="editMenuModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Menu Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editMenuForm" novalidate>
                <div class="modal-body">
                    <input type="hidden" id="editMenuId" name="id">
                    <input type="hidden" id="editMenuOldImage" name="old_image">
                    
                    <!-- Item Name -->
                    <div class="mb-3">
                        <label for="editMenuName" class="form-label">Item Name*</label>
                        <input type="text" class="form-control" id="editMenuName" name="name" 
                               required minlength="3" maxlength="255">
                        <div class="invalid-feedback">Please enter a valid item name (3-255 characters)</div>
                    </div>

                    <!-- Category -->
                    <div class="mb-3">
                        <label for="editMenuCategory" class="form-label">Category*</label>
                        <select class="form-select" id="editMenuCategory" name="category_id" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $index => $category): ?>
                                <option value="<?php echo $index + 1; ?>">
                                    <?php echo htmlspecialchars($category); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Please select a category</div>
                    </div>

                    <!-- Price -->
                    <div class="mb-3">
                        <label for="editMenuPrice" class="form-label">Price*</label>
                        <div class="input-group">
                            <span class="input-group-text">ETB</span>
                            <input type="number" class="form-control" id="editMenuPrice" name="price" 
                                   step="0.01" min="0" required>
                        </div>
                        <div class="invalid-feedback">Please enter a valid price</div>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="editMenuDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editMenuDescription" name="description" 
                                  rows="3" maxlength="1000"></textarea>
                        <div class="form-text">Describe ingredients, preparation, etc.</div>
                    </div>

                    <!-- New Image -->
                    <div class="mb-3">
                        <label for="editMenuImage" class="form-label">New Image</label>
                        <input type="file" class="form-control" id="editMenuImage" name="image" 
                               accept="image/jpeg,image/png,image/webp">
                        <div class="form-text">Leave empty to keep current image</div>
                    </div>

                    <!-- Current Image Preview -->
                    <div id="editImagePreview" class="mb-3">
                        <!-- Current image preview will be shown here -->
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

<!-- Category Management Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manage Categories</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="list-group" id="categoryList">
                    <?php foreach ($categories as $index => $category): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <?php echo htmlspecialchars($category); ?>
                            <span class="badge bg-primary rounded-pill">
                                <?php 
                                    $count_query = "SELECT COUNT(*) as count FROM menu_items WHERE category_id = " . ($index + 1);
                                    $count_result = mysqli_query($connect, $count_query);
                                    $count = mysqli_fetch_assoc($count_result)['count'];
                                    echo $count;
                                ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
