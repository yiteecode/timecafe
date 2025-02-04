<?php
session_start();
require_once '../../db-config.php';
require_once '../includes/auth_check.php';

// Get bookings with optional filters
$where_clause = "1=1";
if (isset($_GET['status'])) {
    $status = mysqli_real_escape_string($connect, $_GET['status']);
    $where_clause .= " AND status = '$status'";
}

if (isset($_GET['date'])) {
    $date = mysqli_real_escape_string($connect, $_GET['date']);
    $where_clause .= " AND DATE(booking_date) = '$date'";
}

$bookings_query = "SELECT * FROM bookings WHERE $where_clause ORDER BY booking_date DESC, booking_time DESC";
$bookings_result = mysqli_query($connect, $bookings_query);

$isSection = true;
include '../includes/header.php';
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Table Bookings</h1>
            <p class="text-muted">Manage restaurant reservations</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form class="row g-3" method="GET">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="accepted">Accepted</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date</label>
                    <input type="date" class="form-control" name="date">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                    <a href="bookings.php" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th>Date & Time</th>
                            <th>Guests</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($booking = mysqli_fetch_assoc($bookings_result)): ?>
                            <tr>
                                <td>#<?php echo $booking['id']; ?></td>
                                <td><?php echo htmlspecialchars($booking['name']); ?></td>
                                <td>
                                    <div><?php echo htmlspecialchars($booking['email']); ?></div>
                                    <div class="small text-muted"><?php echo htmlspecialchars($booking['phone']); ?></div>
                                </td>
                                <td>
                                    <div><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></div>
                                    <div class="small text-muted"><?php echo date('h:i A', strtotime($booking['booking_time'])); ?></div>
                                </td>
                                <td><?php echo $booking['people']; ?> people</td>
                                <td>
                                    <?php
                                    $status_class = [
                                        'pending' => 'bg-warning',
                                        'accepted' => 'bg-success',
                                        'rejected' => 'bg-danger'
                                    ][$booking['status'] ?? 'pending'];
                                    ?>
                                    <span class="badge <?php echo $status_class; ?>">
                                        <?php echo ucfirst($booking['status'] ?? 'pending'); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="viewBooking(<?php echo $booking['id']; ?>)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <?php if (($booking['status'] ?? 'pending') === 'pending'): ?>
                                            <button class="btn btn-sm btn-outline-success" 
                                                    onclick="updateStatus(<?php echo $booking['id']; ?>, 'accepted')">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    onclick="updateStatus(<?php echo $booking['id']; ?>, 'rejected')">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        <?php endif; ?>
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteBooking(<?php echo $booking['id']; ?>)">
                                            <i class="bi bi-trash"></i>
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

<!-- View Booking Modal -->
<div class="modal fade" id="viewBookingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="fw-bold">Customer Name:</label>
                    <div id="bookingName"></div>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Contact Details:</label>
                    <div id="bookingEmail"></div>
                    <div id="bookingPhone"></div>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Reservation:</label>
                    <div id="bookingDateTime"></div>
                    <div id="bookingGuests"></div>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Special Requests:</label>
                    <div id="bookingMessage"></div>
                </div>
                <div>
                    <label class="fw-bold">Booking Status:</label>
                    <div id="bookingStatus"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Booking Scripts -->
<script src="../js/bookings.js"></script>

<?php include '../includes/footer.php'; ?> 