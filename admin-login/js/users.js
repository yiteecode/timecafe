// Create User Form Handler
document.getElementById('createUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'create');

    fetch('../handlers/user_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Success', data.message, 'success');
            location.reload();
        } else {
            throw new Error(data.message || 'Failed to create user');
        }
    })
    .catch(error => {
        showNotification('Error', error.message, 'danger');
    });
});

// Edit User Form Handler
document.getElementById('editUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'update');

    fetch('../handlers/user_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Success', data.message, 'success');
            location.reload();
        } else {
            throw new Error(data.message || 'Failed to update user');
        }
    })
    .catch(error => {
        showNotification('Error', error.message, 'danger');
    });
});

// Edit User Function
function editUser(id) {
    const formData = new FormData();
    formData.append('action', 'get');
    formData.append('id', id);

    fetch('../handlers/user_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.user) {
            const form = document.getElementById('editUserForm');
            form.querySelector('[name="id"]').value = data.user.id;
            form.querySelector('[name="username"]').value = data.user.username;
            form.querySelector('[name="email"]').value = data.user.email;
            form.querySelector('[name="role"]').value = data.user.role;
            form.querySelector('[name="password"]').value = '';

            const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
            modal.show();
        } else {
            throw new Error(data.message || 'Failed to load user details');
        }
    })
    .catch(error => {
        showNotification('Error', error.message, 'danger');
    });
}

// Delete User Function
function deleteUser(id) {
    if (!confirm('Are you sure you want to delete this user?')) return;

    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', id);

    fetch('../handlers/user_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Success', data.message, 'success');
            location.reload();
        } else {
            throw new Error(data.message || 'Failed to delete user');
        }
    })
    .catch(error => {
        showNotification('Error', error.message, 'danger');
    });
}

// Notification Helper
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