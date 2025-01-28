<?php
session_start();

// If already logged in, redirect to dashboard
// if (isset($_SESSION['admin_id'])) {
//     header('Location: admin_dashboard.php');
//     exit;
// }

// Check for login errors
$error = '';
if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Admin Login - Time Cafe</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
	<style>
		body {
			background-color: #f8f9fa;
			height: 100vh;
			display: flex;
			align-items: center;
			justify-content: center;
		}
		.login-container {
			max-width: 400px;
			width: 90%;
			padding: 2rem;
			background: white;
			border-radius: 10px;
			box-shadow: 0 0 20px rgba(0,0,0,0.1);
		}
		.login-header {
			text-align: center;
			margin-bottom: 2rem;
		}
		.login-header img {
			max-width: 150px;
			margin-bottom: 1rem;
		}
	</style>
</head>
<body>
	<div class="login-container">
		<div class="login-header">
			<h2>Time Cafe</h2>
			<p class="text-muted">Admin Panel</p>
		</div>
		
		<?php if ($error): ?>
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
				<?php echo htmlspecialchars($error); ?>
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
		<?php endif; ?>

		<form action="handlers/auth_handler.php" method="POST">
			<div class="mb-3">
				<label for="email" class="form-label">Email</label>
				<input type="email" class="form-control" id="email" name="email" required>
			</div>
			<div class="mb-3">
				<label for="password" class="form-label">Password</label>
				<input type="password" class="form-control" id="password" name="password" required>
			</div>
			<button type="submit" class="btn btn-primary w-100">
				<i class="bi bi-box-arrow-in-right me-2"></i>Login
			</button>
		</form>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>