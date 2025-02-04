<!-- Add Feature Modal -->
<div class="modal fade" id="addFeatureModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Feature</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addFeatureForm" novalidate>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="featureTitle" class="form-label">Title*</label>
                        <input type="text" class="form-control" id="featureTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="featureIcon" class="form-label">Icon*</label>
                        <div class="input-group">
                            <span class="input-group-text">bi-</span>
                            <input type="text" class="form-control" id="featureIcon" name="icon" 
                                   placeholder="star" required>
                        </div>
                        <div class="form-text">
                            Enter icon name without 'bi-' prefix. Example: star, heart, check-circle. 
                            <a href="https://icons.getbootstrap.com/" target="_blank">Browse icons</a>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="featureDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="featureDescription" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Feature</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Feature Modal -->
<div class="modal fade" id="editFeatureModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Feature</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editFeatureForm" novalidate>
                <div class="modal-body">
                    <input type="hidden" id="editFeatureId" name="id">
                    <div class="mb-3">
                        <label for="editFeatureTitle" class="form-label">Title*</label>
                        <input type="text" class="form-control" id="editFeatureTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="editFeatureIcon" class="form-label">Icon*</label>
                        <div class="input-group">
                            <span class="input-group-text">bi-</span>
                            <input type="text" class="form-control" id="editFeatureIcon" name="icon" 
                                   placeholder="star" required>
                        </div>
                        <div class="form-text">
                            Enter icon name without 'bi-' prefix. Example: star, heart, check-circle. 
                            <a href="https://icons.getbootstrap.com/" target="_blank">Browse icons</a>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="editFeatureDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editFeatureDescription" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
