/**
 * Views a message's details
 * @param {number} id - The message ID to view
 */
function viewMessage(id) {
    const formData = new FormData();
    formData.append('action', 'get_message');
    formData.append('id', id);

    fetch('../handlers/contact_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.message) {
            // Populate modal
            document.getElementById('messageFrom').textContent = data.message.name;
            document.getElementById('messageEmail').textContent = data.message.email;
            document.getElementById('messageSubject').textContent = data.message.subject;
            document.getElementById('messageContent').textContent = data.message.message;
            document.getElementById('messageDate').textContent = new Date(data.message.created_at).toLocaleString();

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('viewMessageModal'));
            modal.show();
        } else {
            throw new Error(data.message || 'Failed to load message');
        }
    })
    .catch(error => {
        showNotification('Error', error.message, 'danger');
    });
}

/**
 * Deletes a message
 * @param {number} id - The message ID to delete
 */
function deleteMessage(id) {
    if (!confirm('Are you sure you want to delete this message?')) return;

    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', id);

    fetch('../handlers/contact_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Success', 'Message deleted successfully', 'success');
            location.reload();
        } else {
            throw new Error(data.message || 'Failed to delete message');
        }
    })
    .catch(error => {
        showNotification('Error', error.message, 'danger');
    });
}

// Add read/unread toggle handlers
document.querySelectorAll('.read-toggle').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const formData = new FormData();
        formData.append('action', 'toggle_read');
        formData.append('id', this.dataset.id);

        fetch('../handlers/contact_handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Toggle row styling
                const row = this.closest('tr');
                row.classList.toggle('fw-bold', !this.checked);
            } else {
                throw new Error(data.message || 'Failed to update message status');
            }
        })
        .catch(error => {
            showNotification('Error', error.message, 'danger');
            // Revert checkbox state
            this.checked = !this.checked;
        });
    });
});

// Helper function to show notifications
function showNotification(title, message, type) {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
    notification.style.zIndex = '9999';
    notification.innerHTML = `
        <strong>${title}:</strong> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Select All functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.message-select');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateDeleteButton();
});

// Individual checkbox change handler
document.querySelectorAll('.message-select').forEach(checkbox => {
    checkbox.addEventListener('change', updateDeleteButton);
});

// Update delete button state
function updateDeleteButton() {
    const selectedCount = document.querySelectorAll('.message-select:checked').length;
    const deleteBtn = document.getElementById('deleteSelected');
    deleteBtn.disabled = selectedCount === 0;
    deleteBtn.innerHTML = `<i class="bi bi-trash"></i> Delete Selected (${selectedCount})`;
}

// Delete selected messages
document.getElementById('deleteSelected').addEventListener('click', function() {
    const selectedBoxes = document.querySelectorAll('.message-select:checked');
    if (selectedBoxes.length === 0) return;

    if (!confirm(`Are you sure you want to delete ${selectedBoxes.length} messages?`)) return;

    const ids = Array.from(selectedBoxes).map(box => box.value);

    const formData = new FormData();
    formData.append('action', 'delete_multiple');
    formData.append('ids', JSON.stringify(ids));

    fetch('../handlers/contact_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Success', data.message, 'success');
            location.reload();
        } else {
            throw new Error(data.message || 'Failed to delete messages');
        }
    })
    .catch(error => {
        showNotification('Error', error.message, 'danger');
    });
});

// Fix for read toggle
document.querySelectorAll('.read-toggle').forEach(checkbox => {
    checkbox.addEventListener('change', function(e) {
        e.stopPropagation(); // Prevent event bubbling
        const formData = new FormData();
        formData.append('action', 'toggle_read');
        formData.append('id', this.dataset.id);

        fetch('../handlers/contact_handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const row = this.closest('tr');
                row.classList.toggle('fw-bold', !this.checked);
            } else {
                throw new Error(data.message || 'Failed to update message status');
            }
        })
        .catch(error => {
            showNotification('Error', error.message, 'danger');
            // Revert checkbox state
            this.checked = !this.checked;
        });
    });
}); 