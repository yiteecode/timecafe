/**
 * About Section Management Module
 * Handles all about section functionality including:
 * - Main content updates
 * - Feature management
 * - Image handling
 * - Video URL validation
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeAboutManagement();
});

/**
 * Initializes about section management functionality
 */
function initializeAboutManagement() {
    const mainForm = document.getElementById('aboutForm');
    if (mainForm) {
        mainForm.addEventListener('submit', handleAboutSubmit);
    }

    // Initialize image preview
    const imageInput = document.getElementById('aboutImage');
    if (imageInput) {
        imageInput.addEventListener('change', handleImagePreview);
    }

    // Initialize video URL validation
    const videoInput = document.getElementById('aboutVideo');
    if (videoInput) {
        videoInput.addEventListener('change', validateVideoUrl);
    }
}

/**
 * Handles about form submission
 * @param {Event} event - The form submit event
 */
function handleAboutSubmit(event) {
    event.preventDefault();
    
    const form = this;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass"></i> Saving...';
    
    const formData = new FormData(form);
    formData.append('action', 'update');

    fetch('../handlers/about_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Success', 'About section updated successfully', 'success');
        } else {
            throw new Error(data.message || 'Failed to update about section');
        }
    })
    .catch(error => {
        showNotification('Error', error.message, 'danger');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
}

/**
 * Handles image preview
 * @param {Event} event - The file input change event
 */
function handleImagePreview(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.createElement('img');
            preview.src = e.target.result;
            preview.className = 'img-fluid rounded mt-2';
            
            const container = event.target.parentElement;
            const existingPreview = container.querySelector('img');
            if (existingPreview) {
                container.removeChild(existingPreview);
            }
            container.appendChild(preview);
        };
        reader.readAsDataURL(file);
    }
}

/**
 * Validates YouTube video URL
 * @param {Event} event - The input change event
 */
function validateVideoUrl(event) {
    const url = event.target.value;
    if (url && !isValidYouTubeUrl(url)) {
        showPopupMessage('Please enter a valid YouTube video URL', 'warning');
        event.target.value = '';
    }
}

/**
 * Shows the add feature modal
 */
function showAddFeatureModal() {
    const modal = new bootstrap.Modal(document.getElementById('addFeatureModal'));
    modal.show();
}

/**
 * Handles feature form submission
 * @param {Event} event - The form submit event
 */
function handleFeatureSubmit(event) {
    event.preventDefault();
    
    if (!this.checkValidity()) {
        this.classList.add('was-validated');
        return;
    }

    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass"></i> Saving...';

    const formData = new FormData(this);
    formData.append('action', 'add_feature');

    fetch('../handlers/about_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showPopupMessage('Feature added successfully', 'success');
            location.reload();
        } else {
            throw new Error(data.message || 'Failed to add feature');
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
 * Edits a feature
 * @param {number} id - The feature ID to edit
 */
function editFeature(id) {
    // Show loading state
    const loadingOverlay = document.createElement('div');
    loadingOverlay.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-dark bg-opacity-50';
    loadingOverlay.style.zIndex = '9999';
    loadingOverlay.innerHTML = '<div class="spinner-border text-light" role="status"></div>';
    document.body.appendChild(loadingOverlay);

    // Create FormData for the request
    const formData = new FormData();
    formData.append('action', 'get_feature');
    formData.append('id', id);

    // Change from GET to POST request
    fetch('../handlers/about_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.feature) {
            // Populate the edit form
            const form = document.getElementById('editFeatureForm');
            form.querySelector('#editFeatureId').value = data.feature.id;
            form.querySelector('#editFeatureTitle').value = data.feature.title;
            form.querySelector('#editFeatureDescription').value = data.feature.description || '';
            form.querySelector('#editFeatureIcon').value = data.feature.icon || '';

            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('editFeatureModal'));
            modal.show();
        } else {
            throw new Error(data.message || 'Failed to load feature details');
        }
    })
    .catch(error => {
        showNotification('Error', error.message, 'danger');
    })
    .finally(() => {
        // Remove loading overlay
        document.body.removeChild(loadingOverlay);
    });
}

/**
 * Deletes a feature
 * @param {number} id - The feature ID to delete
 */
function deleteFeature(id) {
    if (!confirm('Are you sure you want to delete this feature?')) return;

    const formData = new FormData();
    formData.append('action', 'delete_feature');
    formData.append('id', id);

    fetch('../handlers/about_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Success', 'Feature deleted successfully', 'success');
            location.reload();
        } else {
            throw new Error(data.message || 'Failed to delete feature');
        }
    })
    .catch(error => {
        showNotification('Error', error.message, 'danger');
    });
}

/**
 * Validates if a URL is a valid YouTube URL
 * @param {string} url - The URL to validate
 * @returns {boolean} - Whether the URL is valid
 */
function isValidYouTubeUrl(url) {
    const pattern = /^(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})$/;
    return pattern.test(url);
}

/**
 * Populates the feature edit form with data
 * @param {Object} feature - The feature data
 */
function populateFeatureForm(feature) {
    const form = document.getElementById('editFeatureForm');
    if (!form) return;

    form.querySelector('#editFeatureId').value = feature.id;
    form.querySelector('#editFeatureTitle').value = feature.title;
    form.querySelector('#editFeatureDescription').value = feature.description;
    form.querySelector('#editFeatureIcon').value = feature.icon;
}

// Helper function to show notifications
function showNotification(title, message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
    notification.style.zIndex = '9999';
    notification.innerHTML = `
        <strong>${title}:</strong> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Add to document
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

function addFeature() {
    const modal = new bootstrap.Modal(document.getElementById('addFeatureModal'));
    modal.show();
}

// Add form submission handler for add feature form
document.getElementById('addFeatureForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass"></i> Adding...';
    
    const formData = new FormData(form);
    formData.append('action', 'add_feature');

    fetch('../handlers/about_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Success', 'Feature added successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('addFeatureModal')).hide();
            location.reload();
        } else {
            throw new Error(data.message || 'Failed to add feature');
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

// Add form submission handler for edit feature form
document.getElementById('editFeatureForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass"></i> Saving...';
    
    const formData = new FormData(form);
    formData.append('action', 'edit_feature');

    fetch('../handlers/about_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Success', 'Feature updated successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('editFeatureModal')).hide();
            location.reload();
        } else {
            throw new Error(data.message || 'Failed to update feature');
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
