// Replace the existing order button click handler with this function
function openOrderModal(itemId, itemName, itemPrice) {
    document.getElementById('menuItemId').value = itemId;
    document.getElementById('menuItemName').value = itemName;
    document.getElementById('selectedItem').textContent = itemName;
    
    const orderModal = new bootstrap.Modal(document.getElementById('orderModal'));
    orderModal.show();
}

// Order form submission
document.getElementById('orderForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);

    fetch('handlers/order_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Hide order modal
            bootstrap.Modal.getInstance(document.getElementById('orderModal')).hide();
            
            // Show success modal
            const successModal = new bootstrap.Modal(document.getElementById('orderSuccessModal'));
            successModal.show();
            
            // Reset form
            this.reset();
        } else {
            throw new Error(data.message);
        }
    })
    .catch(error => {
        alert(error.message || 'Failed to place order. Please try again.');
    });
}); 