// Notification sound
const notificationSound = new Audio('../../assets/sounds/notification.mp3');

// Track seen notifications
let seenNotifications = new Set();

// Function to fetch notifications
function fetchNotifications() {
    fetch('../handlers/notification_handler.php')
        .then(response => response.json())
        .then(data => {
            updateNotificationBadge(data.orders, data.messages);
            updateNotificationDropdown(data.recentOrders);
            
            // Play sound for new orders
            if (data.newOrders > 0 && !seenNotifications.has(data.latestOrderId)) {
                notificationSound.play();
                seenNotifications.add(data.latestOrderId);
            }
        })
        .catch(error => console.error('Error fetching notifications:', error));
}

// Update notification badge
function updateNotificationBadge(orderCount, messageCount) {
    document.getElementById('orderNotificationBadge').textContent = orderCount;
    document.getElementById('messageNotificationBadge').textContent = messageCount;
}

// Update notification dropdown
function updateNotificationDropdown(recentOrders) {
    const dropdown = document.getElementById('notificationDropdown');
    
    if (recentOrders.length === 0) {
        dropdown.innerHTML = '<div class="dropdown-item text-center">No new notifications</div>';
        return;
    }

    let html = '';
    recentOrders.forEach(order => {
        html += `
            <a class="dropdown-item" href="sections/orders.php?id=${order.id}">
                <div class="d-flex align-items-center">
                    <div class="notification-icon bg-primary">
                        <i class="bi bi-cart"></i>
                    </div>
                    <div class="ms-3">
                        <div class="notification-title">New Order #${order.id}</div>
                        <div class="notification-desc">
                            ${order.customer_name} - ${order.items} items
                        </div>
                        <div class="notification-time">
                            ${order.time_ago}
                        </div>
                    </div>
                </div>
            </a>
        `;
    });

    html += `
        <div class="dropdown-divider"></div>
        <a class="dropdown-item text-center" href="sections/orders.php">
            View All Orders
        </a>
    `;

    dropdown.innerHTML = html;
}

// Start periodic checking
setInterval(fetchNotifications, 10000); // Check every 10 seconds
fetchNotifications(); // Initial fetch 