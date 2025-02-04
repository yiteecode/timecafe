<?php
session_start();
require_once '../db-config.php';

// Get current logo
$logoStmt = $connect->prepare("SELECT setting_value FROM settings WHERE setting_key = 'brand_logo'");
$logoStmt->execute();
$logoResult = $logoStmt->get_result();
$currentLogo = $logoResult->fetch_assoc()['setting_value'] ?? 'time-logo.png';

// Add cache-busting parameter
$logoUrl = '../assets/img/' . htmlspecialchars($currentLogo) . '?v=' . time();

// Redirect if already logged in
if (isset($_SESSION['admin_id'])) {
    header('Location: admin_dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Time Cafe - Admin Login</title>
	
	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<!-- Font Awesome -->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
	<!-- Custom CSS -->
	<link href="css/login.css" rel="stylesheet">
</head>
<body>
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<div class="login100-pic js-tilt" data-tilt>
					<img src="<?php echo $logoUrl; ?>" 
						 alt="Time Cafe Logo"
						 onerror="this.src='../assets/img/time-logo.png'">
				</div>

				<form class="login100-form validate-form" action="handlers/auth_handler.php" method="POST">
					<span class="login100-form-title">
						Admin Login
					</span>

					<?php if (isset($_SESSION['login_error'])): ?>
						<div class="alert alert-danger">
							<?php 
								echo $_SESSION['login_error'];
								unset($_SESSION['login_error']);
							?>
						</div>
					<?php endif; ?>

					<div class="wrap-input100 validate-input" data-validate="Valid email is required: ex@abc.xyz">
						<input class="input100" type="email" name="email" placeholder="Email">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
					</div>

					<div class="wrap-input100 validate-input" data-validate="Password is required">
						<input class="input100" type="password" name="password" placeholder="Password">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
					</div>
					
					<div class="container-login100-form-btn">
						<button type="submit" class="login100-form-btn">
							Login
						</button>
					</div>

					<div class="text-center p-t-12">
						<span class="txt1">
							Forgot
						</span>
						<a class="txt2" href="#" onclick="alert('Please contact the system administrator')">
							Password?
						</a>
					</div>

					<div class="text-center p-t-136">
						<a class="txt2" href="../index.php">
							Back to Website
							<i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
						</a>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Scripts -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/tilt.js/1.2.1/tilt.jquery.min.js"></script>
	
	<script src="js/main.js"></script>

	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/tilt/tilt.jquery.min.js"></script>

	<script>
		$('.js-tilt').tilt({
			scale: 1.1
		});
	</script>
</body>
</html>