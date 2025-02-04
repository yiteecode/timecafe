/**
 * Menu Management Module
 * Handles all menu-related functionality including:
 * - Item creation
 * - Item editing
 * - Item deletion
 * - Category management
 * - Image preview
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeMenuManagement();
});

/**
 * Initializes menu management functionality
 */
function initializeMenuManagement() {
    // Initialize form handling
    const form = document.getElementById('menuItemForm');
    if (form) {
        form.addEventListener('submit', handleMenuSubmit);
    }

    // Initialize edit form
    const editForm = document.getElementById('editMenuForm');
    if (editForm) {
        editForm.addEventListener('submit', handleEditSubmit);
    }

    // Initialize image preview
    const imageInput = document.getElementById('menuImage');
    if (imageInput) {
        imageInput.addEventListener('change', handleImagePreview);
    }

    // Initialize sorting if enabled
    initializeSorting();
}

/**
 * Handles menu item form submission
 * @param {Event} event - The form submit event
 */
function handleMenuSubmit(event) {
    event.preventDefault();
    
    if (!this.checkValidity()) {
        this.classList.add('was-validated');
        return;
    }

    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass"></i> Adding...';

    const formData = new FormData(this);
    formData.append('action', 'add');

    fetch('../handlers/menu_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showPopupMessage('Menu item added successfully', 'success');
            this.reset();
            clearImagePreview();
            location.reload();
        } else {
            throw new Error(data.message || 'Failed to add menu item');
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
 * Handles image preview for menu items
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
    const input = document.getElementById('menuImage');
    if (preview) {
        preview.innerHTML = '';
        preview.classList.add('d-none');
    }
    if (input) input.value = '';
}

/**
 * Toggles between grid and list view
 */
function toggleView() {
    const container = document.getElementById('menuItemsGrid');
    container.classList.toggle('list-view');
}

/**
 * Shows the category management modal
 */
function showCategoryModal() {
    const modal = new bootstrap.Modal(document.getElementById('categoryModal'));
    modal.show();
}

function editMenuItem(id) {
    if (!id || isNaN(id)) {  // Add better ID validation
        showNotification('Error', 'Menu item ID is required', 'danger');
        return;
    }

    fetch(`../handlers/menu_handler.php?action=get&id=${encodeURIComponent(id)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.item) {  // Check if item exists
                populateEditForm(data.item);
                const editModal = new bootstrap.Modal(document.getElementById('editMenuModal'));
                editModal.show();
            } else {
                throw new Error(data.message || 'Failed to fetch menu item');
            }
        })
        .catch(error => {
            console.error('Error details:', error);
            showNotification('Error', error.message, 'danger');
        });
}

function populateEditForm(item) {
    if (!item || !item.id) {
        console.error('Invalid item data received');
        showNotification('Error', 'Invalid item data received', 'danger');
        return;
    }

    const form = document.getElementById('editMenuForm');
    if (!form) {
        console.error('Edit form not found');
        return;
    }

    try {
        // Set hidden ID field
        const idInput = form.querySelector('input[name="id"]');
        if (!idInput) {
            throw new Error('ID input field not found');
        }
        idInput.value = item.id;

        // Set other form fields with more specific selectors
        const nameInput = form.querySelector('input[name="name"]');
        const descriptionInput = form.querySelector('textarea[name="description"]');
        const priceInput = form.querySelector('input[name="price"]');
        const categoryInput = form.querySelector('select[name="category_id"]');

        if (nameInput) nameInput.value = item.name || '';
        if (descriptionInput) descriptionInput.value = item.description || '';
        if (priceInput) priceInput.value = item.price || '';
        if (categoryInput) categoryInput.value = item.category_id || '';

        // Debug log to check values
        console.log('Item data:', item);
        
        // Show current image if exists
        const currentImageContainer = form.querySelector('.current-image');
        if (currentImageContainer && item.image) {
            currentImageContainer.innerHTML = `
                <div class="mb-3">
                    <label class="form-label">Current Image:</label>
                    <img src="../../uploads/menu/${item.image}" 
                         class="img-thumbnail" 
                         style="max-height: 100px; display: block;">
                    <small class="text-muted">Current: ${item.image}</small>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error populating form:', error);
        showNotification('Error', 'Failed to populate form fields: ' + error.message, 'danger');
    }
}

// Handle edit form submission
document.getElementById('editMenuForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Updating...';
    
    const formData = new FormData(form);
    formData.append('action', 'update');

    fetch('../handlers/menu_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Success', 'Menu item updated successfully', 'success');
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('editMenuModal')).hide();
            // Reload the page instead of calling loadMenuItems
            window.location.reload();
        } else {
            throw new Error(data.message || 'Failed to update menu item');
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

function showNotification(title, message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast align-items-center border-0 bg-${type} text-white`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <strong>${title}</strong><br>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    const container = document.createElement('div');
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '1050';
    container.appendChild(toast);
    document.body.appendChild(container);

    const bsToast = new bootstrap.Toast(toast, {
        animation: true,
        autohide: true,
        delay: 3000
    });
    
    bsToast.show();

    toast.addEventListener('hidden.bs.toast', () => {
        container.remove();
    });
}

function deleteMenuItem(id) {
    // Create and show a confirmation modal
    const confirmModal = document.createElement('div');
    confirmModal.className = 'modal fade';
    confirmModal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this menu item? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(confirmModal);
    
    const modal = new bootstrap.Modal(confirmModal);
    modal.show();
    
    // Handle delete confirmation
    document.getElementById('confirmDelete').addEventListener('click', function() {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);

        // Show loading state
        this.disabled = true;
        this.innerHTML = '<i class="bi bi-hourglass-split"></i> Deleting...';

        fetch('../handlers/menu_handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showNotification('Success', 'Menu item deleted successfully', 'success');
                modal.hide();
                window.location.reload();
            } else {
                throw new Error(data.message || 'Failed to delete menu item');
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            showNotification('Error', error.message, 'danger');
        })
        .finally(() => {
            modal.hide();
            confirmModal.remove();
        });
    });

    // Clean up modal when hidden
    confirmModal.addEventListener('hidden.bs.modal', function() {
        confirmModal.remove();
    });
}

function filterByCategory(categoryId) {
    const items = document.querySelectorAll('.menu-item');
    items.forEach(item => {
        if (!categoryId || item.dataset.category === categoryId) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
}

function initializeSorting() {
    const container = document.getElementById('menuItemsGrid');
    if (container && typeof Sortable !== 'undefined') {
        new Sortable(container, {
            animation: 150,
            handle: '.sort-handle',
            onEnd: function(evt) {
                updateMenuOrder();
            }
        });
    }
}

function updateMenuOrder() {
    const items = document.querySelectorAll('.menu-item');
    const orderData = Array.from(items).map((item, index) => ({
        id: item.dataset.id,
        order: index
    }));

    fetch('../handlers/menu_handler.php', {
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

function showPopupMessage(message, type) {
    // You can implement a more sophisticated message display
    alert(message);
}

// Add this new function for handling new menu item submission
document.getElementById('addMenuForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Adding...';
    
    const formData = new FormData(form);
    formData.append('action', 'add');  // Set the correct action

    fetch('../handlers/menu_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Success', 'Menu item added successfully', 'success');
            form.reset(); // Reset the form
            clearImagePreview(); // Clear the image preview
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('addMenuModal')).hide();
            // Reload the page
            window.location.reload();
        } else {
            throw new Error(data.message || 'Failed to add menu item');
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
