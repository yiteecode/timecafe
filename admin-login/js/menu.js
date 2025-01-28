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
    fetch(`../handlers/menu_handler.php?action=get&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateEditForm(data.item);
                const modal = new bootstrap.Modal(document.getElementById('editMenuModal'));
                modal.show();
            } else {
                throw new Error(data.message || 'Failed to load menu item');
            }
        })
        .catch(error => {
            showPopupMessage(error.message, 'danger');
        });
}

function populateEditForm(item) {
    document.getElementById('editMenuId').value = item.id;
    document.getElementById('editMenuName').value = item.name;
    document.getElementById('editMenuCategory').value = item.category_id;
    document.getElementById('editMenuPrice').value = item.price;
    document.getElementById('editMenuDescription').value = item.description;
    document.getElementById('editMenuOldImage').value = item.image;

    const preview = document.getElementById('editImagePreview');
    if (item.image) {
        preview.innerHTML = `
            <img src="../../uploads/menu/${item.image}" class="img-fluid rounded" alt="Current image">
        `;
    } else {
        preview.innerHTML = '';
    }
}

function deleteMenuItem(id) {
    if (!confirm('Are you sure you want to delete this menu item?')) return;

    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', id);

    fetch('../handlers/menu_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showPopupMessage('Menu item deleted successfully', 'success');
            location.reload();
        } else {
            throw new Error(data.message || 'Failed to delete menu item');
        }
    })
    .catch(error => {
        showPopupMessage(error.message, 'danger');
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
