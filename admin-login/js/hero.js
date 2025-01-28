/**
 * Hero Section Management Module
 * Handles hero section content updates including:
 * - Text content updates
 * - Image upload
 * - Video upload
 * - Preview functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeHeroManagement();
});

/**
 * Initializes hero section management functionality
 */
function initializeHeroManagement() {
    const form = document.getElementById('heroForm');
    if (!form) return;

    // Initialize form submission
    form.addEventListener('submit', handleHeroSubmit);

    // Initialize real-time preview
    form.querySelector('#heading').addEventListener('input', updateTextPreview);
    form.querySelector('#subheading').addEventListener('input', updateTextPreview);

    // Initialize file previews
    const imageInput = document.getElementById('heroImage');
    const videoInput = document.getElementById('heroVideo');
    
    if (imageInput) imageInput.addEventListener('change', previewImage, previewVideo);
    if (videoInput) videoInput.addEventListener('change', previewImage,previewVideo);
}

/**
 * Handles hero form submission
 * @param {Event} event - The form submit event
 */
function handleHeroSubmit(event) {
    event.preventDefault();
    const form = event.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;

    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving...';

    const formData = new FormData(form);

    fetch('../handlers/hero_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Success', data.message, 'success');
            updatePreview(data.data);
        } else {
            throw new Error(data.message);
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
function previewImage(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        const previewBox = document.querySelector('.preview-box');
        if (previewBox) {
            previewBox.innerHTML = `
                <div class="preview-image-container">
                    <img src="${e.target.result}" alt="Preview" class="preview-media">
                </div>
            `;
        }
    };
    reader.readAsDataURL(file);
}

/**
 * Handles video preview
 * @param {Event} event - The file input change event
 */
function previewVideo(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        const previewBox = document.querySelector('.preview-box');
        if (previewBox) {
            previewBox.innerHTML = `
                <div class="preview-video-container">
                    <video class="preview-media" controls>
                        <source src="${e.target.result}" type="${file.type}">
                        Your browser does not support the video tag.
                    </video>
                </div>
            `;
        }
    };
    reader.readAsDataURL(file);
}

function updateTextPreview(event) {
    const field = event.target;
    const value = field.value;
    
    if (field.id === 'heading') {
        document.querySelector('.preview-heading').textContent = value || 'Your Heading Here';
    } else if (field.id === 'subheading') {
        document.querySelector('.preview-subheading').textContent = value || 'Your subheading text will appear here';
    }
}

function updatePreview(data) {
    const headingElement = document.querySelector('.preview-heading');
    const subheadingElement = document.querySelector('.preview-subheading');
    
    if (headingElement) {
        headingElement.textContent = data.heading;
    }
    if (subheadingElement) {
        subheadingElement.textContent = data.subheading;
    }

    // Update media preview if needed
    if (data.hero_image) {
        const previewBox = document.querySelector('.preview-box');
        if (previewBox) {
            previewBox.innerHTML = `
                <div class="preview-image-container">
                    <img src="../../uploads/hero/${data.hero_image}" alt="Preview" class="preview-media">
                </div>
            `;
        }
    }
}

function updateCtaPreview() {
    const ctaStyle = document.querySelector('input[name="cta_style"]:checked').value;
    const ctaButton = document.querySelector('.preview-content .btn');
    
    if (ctaButton) {
        ctaButton.className = `btn btn-${ctaStyle === 'outline' ? 'outline-primary' : 'primary'}`;
    }
}

function setPreviewSize(size) {
    const previewContainer = document.getElementById('previewContainer');
    previewContainer.className = `preview-box position-relative ${size}`;
}

function resetForm() {
    if (confirm('Are you sure you want to reset all changes?')) {
        document.getElementById('heroForm').reset();
        clearImagePreview();
        clearVideoPreview();
        updatePreview();
    }
}

function showNotification(title, message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0 show`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <strong>${title}:</strong> ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    const container = document.createElement('div');
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '1050';
    container.appendChild(toast);
    document.body.appendChild(container);

    setTimeout(() => {
        toast.remove();
        container.remove();
    }, 3000);
}
