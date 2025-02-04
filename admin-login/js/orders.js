// Filter orders by status
function filterOrders(status) {
    window.location.href = `orders.php${status ? '?status=' + status : ''}`;
}

// Update order status
function updateOrderStatus(orderId, status) {
    if (!confirm(`Are you sure you want to mark this order as ${status}?`)) return;

    const formData = new FormData();
    formData.append('id', orderId);
    formData.append('status', status);

    fetch('../handlers/order_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Success', data.message, 'success');
            location.reload();
        } else {
            throw new Error(data.message);
        }
    })
    .catch(error => {
        showNotification('Error', error.message, 'danger');
    });
}

// Show notification
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