/**
 * Chefs Management Module
 * Handles all chef-related functionality including:
 * - Chef addition
 * - Chef editing
 * - Chef deletion
 * - Image preview
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeChefsManagement();
});

/**
 * Initializes chefs management functionality
 */
function initializeChefsManagement() {
    // Initialize form handling
    const form = document.getElementById('chefForm');
    if (form) {
        form.addEventListener('submit', handleChefSubmit);
    }

    // Initialize edit form handling
    const editForm = document.getElementById('editChefForm');
    if (editForm) {
        editForm.addEventListener('submit', handleEditSubmit);
    }

    // Initialize image preview
    const imageInput = document.getElementById('chefImage');
    if (imageInput) {
        imageInput.addEventListener('change', handleImagePreview);
    }

    // Initialize edit image preview
    const editImageInput = document.getElementById('editChefImage');
    if (editImageInput) {
        editImageInput.addEventListener('change', handleImagePreview);
    }

    // Initialize sorting
    initializeSorting();
}

/**
 * Handles chef form submission
 * @param {Event} event - The form submit event
 */
function handleChefSubmit(event) {
    event.preventDefault();
    
    const form = event.target;
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }

    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass"></i> Adding...';

    const formData = new FormData(form);
    formData.append('action', 'add');

    fetch('../handlers/chef_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Server response:', text);
            throw new Error('Server returned invalid data');
        }
        
        if (data.success) {
            showSuccessMessage(data.chef);
            form.reset();
            clearImagePreview();
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            throw new Error(data.message || 'Failed to add chef');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorMessage(error.message);
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-plus-circle me-2"></i>Add Chef';
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
    const input = document.getElementById('chefImage');
    if (preview) {
        preview.innerHTML = '';
        preview.classList.add('d-none');
    }
    if (input) input.value = '';
}

/**
 * Populates the edit form with chef data
 * @param {Object} chef - The chef data to populate the form with
 */
function populateEditForm(chef) {
    // Get the form elements
    const form = document.getElementById('editChefForm');
    if (!form) {
        console.error('Edit form not found');
        return;
    }

    // Set form values
    form.querySelector('#editChefId').value = chef.id;
    form.querySelector('#editChefName').value = chef.name;
    form.querySelector('#editChefProfession').value = chef.profession;
    form.querySelector('#editChefDescription').value = chef.description || '';
    form.querySelector('#editChefFacebook').value = chef.facebook || '';
    form.querySelector('#editChefInstagram').value = chef.instagram || '';
    form.querySelector('#editChefTwitter').value = chef.twitter || '';

    // Add submit event listener
    form.removeEventListener('submit', handleEditSubmit); // Remove any existing listener
    form.addEventListener('submit', handleEditSubmit);
}

/**
 * Opens the edit chef modal
 * @param {number} id - The chef ID to edit
 */
function editChef(id) {
    fetch(`../handlers/chef_handler.php?action=get&id=${id}`)
        .then(response => response.text())
        .then(text => {
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error('Server response:', text);
                throw new Error('Server returned invalid data');
            }
            
            if (data.success) {
                const modal = new bootstrap.Modal(document.getElementById('editChefModal'));
                populateEditForm(data.data);
                modal.show();
            } else {
                throw new Error(data.message || 'Failed to load chef details');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorMessage(error.message);
        });
}

/**
 * Handles edit form submission
 * @param {Event} event - The form submit event
 */
function handleEditSubmit(event) {
    event.preventDefault();
    
    const form = event.target;
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }

    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass"></i> Saving...';

    const formData = new FormData(form);
    formData.append('action', 'edit');

    fetch('../handlers/chef_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    })
    .then(text => {
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Server response:', text);
            throw new Error('Failed to parse server response');
        }

        if (data.success) {
            // Show success message
            showSuccessMessage({
                name: data.chef.name,
                profession: 'Updated successfully!'
            });

            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('editChefModal'));
            if (modal) {
                modal.hide();
            }

            // Reload page after delay
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            throw new Error(data.message || 'Failed to update chef');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorMessage(error.message);
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-save"></i> Save Changes';
    });
}

/**
 * Deletes a chef
 * @param {number} id - The chef ID to delete
 */
function deleteChef(id) {
    if (!confirm('Are you sure you want to delete this chef?')) return;

    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', id);

    fetch('../handlers/chef_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Server response:', text);
            throw new Error('Server returned invalid data');
        }
        
        if (data.success) {
            showSuccessMessage({ name: 'Chef', profession: 'Deleted successfully' });
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            throw new Error(data.message || 'Failed to delete chef');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorMessage(error.message);
    });
}

function showPopupMessage(message, type = 'info') {
    // Create a Bootstrap alert
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
    alertDiv.role = 'alert';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    document.body.appendChild(alertDiv);

    // Remove the alert after 3 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}

function showSuccessMessage(chef) {
    const messageContainer = document.createElement('div');
    messageContainer.className = 'success-message position-fixed top-50 start-50 translate-middle';
    messageContainer.style.zIndex = '1050';
    
    messageContainer.innerHTML = `
        <div class="card shadow-lg">
            <div class="card-body text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                </div>
                <h5 class="card-title">Chef Added Successfully!</h5>
                <p class="card-text mb-0">${chef.name}</p>
                <p class="text-muted">${chef.profession}</p>
            </div>
        </div>
    `;

    document.body.appendChild(messageContainer);

    // Add some CSS
    const style = document.createElement('style');
    style.textContent = `
        .success-message {
            animation: fadeIn 0.3s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translate(-50%, -60%); }
            to { opacity: 1; transform: translate(-50%, -50%); }
        }
    `;
    document.head.appendChild(style);

    // Remove after delay
    setTimeout(() => {
        messageContainer.style.opacity = '0';
        messageContainer.style.transition = 'opacity 0.3s ease-out';
        setTimeout(() => messageContainer.remove(), 300);
    }, 1500);
}

function showErrorMessage(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'alert alert-danger alert-dismissible fade show position-fixed top-0 end-0 m-3';
    errorDiv.style.zIndex = '1050';
    errorDiv.innerHTML = `
        <i class="bi bi-exclamation-circle me-2"></i>${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    document.body.appendChild(errorDiv);

    setTimeout(() => {
        errorDiv.classList.remove('show');
        setTimeout(() => errorDiv.remove(), 300);
    }, 3000);
}

// ... Additional chef management functions
