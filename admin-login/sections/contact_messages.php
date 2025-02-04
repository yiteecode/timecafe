<?php
session_start();
require_once '../../db-config.php';
require_once '../includes/auth_check.php';

// Get contact messages
$messages_query = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$messages_result = mysqli_query($connect, $messages_query);

$isSection = true;
include '../includes/header.php';
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Contact Messages</h1>
            <p class="text-muted">Manage contact form submissions</p>
        </div>
    </div>

    <!-- Messages Table -->
    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <button class="btn btn-danger" id="deleteSelected" disabled>
                    <i class="bi bi-trash"></i> Delete Selected
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </div>
                            </th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($message = mysqli_fetch_assoc($messages_result)): ?>
                            <tr class="<?php echo isset($message['is_read']) && $message['is_read'] ? '' : 'fw-bold'; ?>">
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input message-select" 
                                               value="<?php echo $message['id']; ?>">
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($message['name']); ?></td>
                                <td><?php echo htmlspecialchars($message['email']); ?></td>
                                <td><?php echo htmlspecialchars($message['subject']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($message['created_at'])); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" 
                                            onclick="viewMessage(<?php echo $message['id']; ?>)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" 
                                            onclick="deleteMessage(<?php echo $message['id']; ?>)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- View Message Modal -->
<div class="modal fade" id="viewMessageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Message Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="fw-bold">From:</label>
                    <div id="messageFrom"></div>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Email:</label>
                    <div id="messageEmail"></div>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Subject:</label>
                    <div id="messageSubject"></div>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Message:</label>
                    <div id="messageContent"></div>
                </div>
                <div>
                    <label class="fw-bold">Received:</label>
                    <div id="messageDate"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contact Messages Scripts -->
<script src="../js/contact_messages.js"></script>

<?php include '../includes/footer.php'; ?> 