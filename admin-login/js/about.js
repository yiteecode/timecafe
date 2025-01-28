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
    
    if (!this.checkValidity()) {
        this.classList.add('was-validated');
        return;
    }

    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass"></i> Saving...';

    const formData = new FormData(this);
    formData.append('action', 'update');

    fetch('../handlers/about_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showPopupMessage('About section updated successfully', 'success');
            location.reload();
        } else {
            throw new Error(data.message || 'Failed to update about section');
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
    fetch(`../handlers/about_handler.php?action=get_feature&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const modal = new bootstrap.Modal(document.getElementById('editFeatureModal'));
                populateFeatureForm(data.data);
                modal.show();
            } else {
                throw new Error(data.message || 'Failed to load feature details');
            }
        })
        .catch(error => {
            showPopupMessage(error.message, 'danger');
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
            showPopupMessage('Feature deleted successfully', 'success');
            location.reload();
        } else {
            throw new Error(data.message || 'Failed to delete feature');
        }
    })
    .catch(error => {
        showPopupMessage(error.message, 'danger');
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
