<!DOCTYPE html>

<?php
session_start();
include('config.php');
?>

<html data-theme="dark">

<head>
	<title>Poker | Login</title>
	<?php include('head.php'); ?>
	<!--Stylesheets-->
	<link rel="stylesheet" type="text/css" href="style/dark.css"/>
</head>

<body>

<?php
if (isset($_POST['login']) && $_SERVER["REQUEST_METHOD"] == "POST") {
	
	$username = $_POST['username'];
	$password = $_POST['password'];
	
	$query = $db->prepare("SELECT * FROM `users` WHERE `username`=:username");
	$query->execute([ ':username' => $username, ]);
	
	$user = $query->fetch(PDO::FETCH_ASSOC);
	
	if ($user && password_verify($password, $user['password'])) {
		$_SESSION['user_id'] = $user['id'];
		header('Location: /poker/index');
	} else {
		echo '<p class="error">Wrong username or password!</p>';
	}
}
?>

	<div class="login-container">
		<div class="login-wrapper">
			<form class="login-form" method="POST">
				<span class="login-form-title">Login</span>
				<div class="login-input-wrap">
					<span class="login-label">Username</span>
					<input class="login-input" type="text" name="username" placeholder="Enter your username" data-validate="Username is required" required>
					<span class="focus-input" data-symbol="&#xf206;"></span>
				</div>
				<div class="login-input-wrap">
					<span class="login-label">Password</span>
					<input class="login-input" type="password" name="password" placeholder="Enter your password" data-validate="Password is required">
					<span class="focus-input" data-symbol="&#xf190;"></span>
				</div>
				<div class="text-right">
					<a href="javascript: delay('reset')">Forgot password?</a>
				</div>
				<div class="login-submit">
					<div class="login-submit-cont">
						<div class="login-submit-background"></div>
						<input class="login-submit-btn" type="submit" name="login" value="LOGIN" />
					</div>
				</div>
				<span class="login-sign-up1">Or Sign Up Using</span>
				<span class="login-sign-up2"><a href="javascript:delay('sign_up')">Sign Up</a></span>
				<div class="toggle-container">
					<input type="checkbox" id="switch" name="theme"/><label for="switch">Toggle</label>
				</div>
			</form>
		</div>
	</div>
	<script src="js/theme.js"></script>
</body>
</html>
