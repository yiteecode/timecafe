<!-- Edit Gallery Modal -->
<div class="modal fade" id="editGalleryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Gallery Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editGalleryForm" novalidate>
                <div class="modal-body">
                    <input type="hidden" id="editGalleryId" name="id">
                    <input type="hidden" id="editGalleryOldImage" name="old_image">
                    
                    <div class="mb-3">
                        <label for="editGalleryTitle" class="form-label">Title*</label>
                        <input type="text" class="form-control" id="editGalleryTitle" name="title" 
                               required minlength="3" maxlength="100">
                        <div class="invalid-feedback">Please enter a title (3-100 characters)</div>
                    </div>

                    <div class="mb-3">
                        <label for="editGalleryDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editGalleryDescription" name="description" 
                                  rows="3" maxlength="500"></textarea>
                        <div class="form-text">Maximum 500 characters</div>
                    </div>

                    <div class="mb-3">
                        <label for="editGalleryImage" class="form-label">New Image</label>
                        <input type="file" class="form-control" id="editGalleryImage" name="image" 
                               accept="image/jpeg,image/png,image/webp">
                        <div class="form-text">Leave empty to keep current image</div>
                    </div>

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
