<?php
session_start();
require_once '../../db-config.php';
require_once '../includes/auth_check.php';

// Get orders with optional filters
$where_clause = "1=1";
if (isset($_GET['status'])) {
    $status = mysqli_real_escape_string($connect, $_GET['status']);
    $where_clause .= " AND o.status = '$status'";
}

$orders_query = "
    SELECT o.*, m.name as menu_item_name, m.price 
    FROM orders o
    JOIN menu_items m ON o.menu_item_id = m.id
    WHERE $where_clause 
    ORDER BY o.created_at DESC";
$orders_result = mysqli_query($connect, $orders_query);

$isSection = true;
include '../includes/header.php';
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Order Management</h1>
            <p class="text-muted">Manage customer orders and their status</p>
        </div>
        <div>
            <select class="form-select" id="statusFilter" onchange="filterOrders(this.value)">
                <option value="">All Orders</option>
                <option value="pending">Pending</option>
                <option value="processing">Processing</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Menu Item</th>
                            <th>Quantity</th>
                            <th>Contact</th>
                            <th>Address</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($order['menu_item_name']); ?>
                                    <div class="small text-muted">
                                        ETB <?php echo number_format($order['price'], 2); ?>
                                    </div>
                                </td>
                                <td><?php echo $order['quantity']; ?></td>
                                <td>
                                    <a href="mailto:<?php echo htmlspecialchars($order['email']); ?>" 
                                       class="d-block text-decoration-none">
                                        <i class="bi bi-envelope"></i> 
                                        <?php echo htmlspecialchars($order['email']); ?>
                                    </a>
                                    <a href="tel:<?php echo htmlspecialchars($order['phone']); ?>" 
                                       class="d-block text-decoration-none">
                                        <i class="bi bi-telephone"></i> 
                                        <?php echo htmlspecialchars($order['phone']); ?>
                                    </a>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($order['address']); ?>
                                    <?php if (!empty($order['special_instructions'])): ?>
                                        <div class="small text-muted">
                                            <strong>Note:</strong> 
                                            <?php echo htmlspecialchars($order['special_instructions']); ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo match($order['status']) {
                                            'pending' => 'warning',
                                            'processing' => 'info',
                                            'completed' => 'success',
                                            'cancelled' => 'danger',
                                            default => 'secondary'
                                        };
                                    ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-primary"
                                                onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'processing')">
                                            Process
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-success"
                                                onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'completed')">
                                            Complete
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'cancelled')">
                                            Cancel
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="../js/orders.js"></script>

<?php include '../includes/footer.php'; ?> 