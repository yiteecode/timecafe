/**
 * Gallery Management Module
 * Handles all gallery-related functionality including:
 * - Image upload with preview
 * - Gallery item editing
 * - Gallery item deletion
 * - Image preview
 * - Sorting functionality
 */

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeGalleryManagement();
});

/**
 * Initializes gallery management functionality
 */
function initializeGalleryManagement() {
    // Initialize form handling
    const form = document.getElementById('galleryForm');
    if (form) {
        form.addEventListener('submit', handleGallerySubmit);
    }

    // Initialize image preview
    const imageInput = document.getElementById('galleryImage');
    if (imageInput) {
        imageInput.addEventListener('change', handleImagePreview);
    }

    // Initialize sorting
    initializeSorting();
}

/**
 * Handles gallery form submission
 * @param {Event} event - The form submit event
 */
function handleGallerySubmit(event) {
    event.preventDefault();
    
    if (!this.checkValidity()) {
        this.classList.add('was-validated');
        return;
    }

    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass"></i> Uploading...';

    const formData = new FormData(this);
    formData.append('action', 'add');

    fetch('../handlers/gallery_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showPopupMessage('Gallery item added successfully', 'success');
            this.reset();
            clearImagePreview();
            location.reload();
        } else {
            throw new Error(data.message || 'Failed to add gallery item');
        }
    })
    .catch(error => {
        showPopupMessage(error.message, 'danger');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

/**
 * Handles image preview
 * @param {Event} event - The file input change event
 */
function handleImagePreview(event) {
    const preview = document.getElementById('imagePreviewContainer');
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `
                <div class="position-relative">
                    <img src="${e.target.result}" class="img-fluid rounded" alt="Preview">
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" 
                            onclick="clearImagePreview()">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            `;
            preview.classList.remove('d-none');
        };
        reader.readAsDataURL(this.files[0]);
    }
}

/**
 * Clears the image preview
 */
function clearImagePreview() {
    const preview = document.getElementById('imagePreviewContainer');
    const input = document.getElementById('galleryImage');
    if (preview) {
        preview.innerHTML = '';
        preview.classList.add('d-none');
    }
    if (input) input.value = '';
}

/**
 * Opens the edit gallery modal
 * @param {number} id - The gallery item ID to edit
 */
function editGalleryItem(id) {
    fetch(`../handlers/gallery_handler.php?action=get&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.item) {
                // Populate the edit form
                const form = document.getElementById('editGalleryForm');
                form.querySelector('#editGalleryId').value = data.item.id;
                form.querySelector('#editGalleryTitle').value = data.item.title;
                form.querySelector('#editGalleryDescription').value = data.item.description || '';
                
                // Show current image if exists
                const imagePreview = document.getElementById('editImagePreview');
                if (data.item.image) {
                    imagePreview.innerHTML = `
                        <div class="mb-3">
                            <label class="form-label">Current Image:</label>
                            <img src="../../uploads/gallery/${data.item.image}" 
                                 class="img-thumbnail" 
                                 style="max-height: 200px;">
                            <input type="hidden" name="old_image" value="${data.item.image}">
                        </div>
                    `;
                } else {
                    imagePreview.innerHTML = '';
                }

                // Show the modal
                const modal = new bootstrap.Modal(document.getElementById('editGalleryModal'));
                modal.show();
            } else {
                throw new Error(data.message || 'Failed to load gallery item');
            }
        })
        .catch(error => {
            showNotification('Error', error.message, 'danger');
        });
}

/**
 * Handles gallery item update
 * @param {Event} event - The form submit event
 */
function handleGalleryUpdate(event) {
    event.preventDefault();
    
    const form = event.target;
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }

    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass"></i> Saving...';

    const formData = new FormData(form);
    formData.append('action', 'edit');

    fetch('../handlers/gallery_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showPopupMessage('Gallery item updated successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('editGalleryModal')).hide();
            location.reload();
        } else {
            throw new Error(data.message || 'Failed to update gallery item');
        }
    })
    .catch(error => {
        showPopupMessage(error.message, 'danger');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

/**
 * Deletes a gallery item
 * @param {number} id - The gallery item ID to delete
 */
function deleteGalleryItem(id) {
    if (!confirm('Are you sure you want to delete this gallery item?')) return;

    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', id);

    fetch('../handlers/gallery_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showPopupMessage('Gallery item deleted successfully', 'success');
            location.reload();
        } else {
            throw new Error(data.message || 'Failed to delete gallery item');
        }
    })
    .catch(error => {
        showPopupMessage(error.message, 'danger');
    });
}

/**
 * Initializes sorting functionality
 */
function initializeSorting() {
    const container = document.getElementById('galleryItemsGrid');
    if (container && typeof Sortable !== 'undefined') {
        new Sortable(container, {
            animation: 150,
            handle: '.sort-handle',
            onEnd: function(evt) {
                updateGalleryOrder();
            }
        });
    }
}

/**
 * Updates gallery items order
 */
function updateGalleryOrder() {
    const items = document.querySelectorAll('.gallery-item');
    const orderData = Array.from(items).map((item, index) => ({
        id: item.dataset.id,
        order: index
    }));

    fetch('../handlers/gallery_handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            action: 'update_order',
            items: orderData
        })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            throw new Error(data.message || 'Failed to update order');
        }
    })
    .catch(error => {
        showPopupMessage(error.message, 'danger');
    });
}

function toggleView() {
    const container = document.getElementById('galleryItemsGrid');
    container.classList.toggle('list-view');
}

function showPopupMessage(message, type) {
    // Implement your popup message display logic here
    alert(message);
}

// Add form submission handler for edit form
document.getElementById('editGalleryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass"></i> Saving...';
    
    const formData = new FormData(form);
    formData.append('action', 'edit');

    fetch('../handlers/gallery_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Success', 'Gallery item updated successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('editGalleryModal')).hide();
            location.reload();
        } else {
            throw new Error(data.message || 'Failed to update gallery item');
        }
    })
    .catch(error => {
        showNotification('Error', error.message, 'danger');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
});

// ... Additional gallery functions (edit, delete, etc.) 