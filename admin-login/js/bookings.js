/**
 * Views booking details
 * @param {number} id - The booking ID to view
 */
function viewBooking(id) {
    const formData = new FormData();
    formData.append('action', 'get_booking');
    formData.append('id', id);

    fetch('../handlers/booking_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.booking) {
            // Format date and time
            const bookingDate = new Date(data.booking.booking_date);
            const bookingTime = new Date(`2000-01-01T${data.booking.booking_time}`);
            
            // Populate modal
            document.getElementById('bookingName').textContent = data.booking.name;
            document.getElementById('bookingEmail').textContent = data.booking.email;
            document.getElementById('bookingPhone').textContent = data.booking.phone;
            document.getElementById('bookingDateTime').innerHTML = `
                ${bookingDate.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}
                <br>
                ${bookingTime.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}
            `;
            document.getElementById('bookingGuests').textContent = `${data.booking.people} people`;
            document.getElementById('bookingMessage').textContent = data.booking.message || 'No special requests';
            document.getElementById('bookingStatus').innerHTML = `
                <span class="badge bg-${getStatusColor(data.booking.status)}">
                    ${data.booking.status ? data.booking.status.toUpperCase() : 'PENDING'}
                </span>
            `;

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('viewBookingModal'));
            modal.show();
        } else {
            throw new Error(data.message || 'Failed to load booking details');
        }
    })
    .catch(error => {
        showNotification('Error', error.message, 'danger');
    });
}

/**
 * Updates booking status
 * @param {number} id - The booking ID to update
 * @param {string} status - The new status
 */
function updateStatus(id, status) {
    if (!confirm(`Are you sure you want to ${status} this booking?`)) return;

    const formData = new FormData();
    formData.append('action', 'update_status');
    formData.append('id', id);
    formData.append('status', status);

    fetch('../handlers/booking_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Success', data.message, 'success');
            location.reload();
        } else {
            throw new Error(data.message || 'Failed to update booking status');
        }
    })
    .catch(error => {
        showNotification('Error', error.message, 'danger');
    });
}

/**
 * Deletes a booking
 * @param {number} id - The booking ID to delete
 */
function deleteBooking(id) {
    if (!confirm('Are you sure you want to delete this booking?')) return;

    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', id);

    fetch('../handlers/booking_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Success', data.message, 'success');
            location.reload();
        } else {
            throw new Error(data.message || 'Failed to delete booking');
        }
    })
    .catch(error => {
        showNotification('Error', error.message, 'danger');
    });
}

/**
 * Gets the appropriate status color
 * @param {string} status - The booking status
 * @returns {string} The Bootstrap color class
 */
function getStatusColor(status) {
    switch (status) {
        case 'accepted':
            return 'success';
        case 'rejected':
            return 'danger';
        default:
            return 'warning';
    }
}

/**
 * Shows a notification
 * @param {string} title - The notification title
 * @param {string} message - The notification message
 * @param {string} type - The notification type
 */
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