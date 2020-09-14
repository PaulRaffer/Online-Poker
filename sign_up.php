<!DOCTYPE HTML>

<?php
session_start();
include('config.php');
?>

<html data-theme="dark">

<head>
	<title>Poker | Sign Up</title>
	<?php include('head.php'); ?>
	<!--Stylesheets-->
	<link rel="stylesheet" type="text/css" href="style/dark.css" title="Dark" />
</head>

<body>

<?php
if (isset($_POST['register']) && $_SERVER["REQUEST_METHOD"] == "POST") {
	
	$username = $_POST['username'];
	$password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
	
	$query = $db->prepare("SELECT * FROM `users` WHERE `username`=:username");
	$query->execute([ ':username' => $username ]);
	
	if ($query->rowCount() > 0) {
		echo "<p class=\"error\">Username '$username' already exists!</p>";
	} elseif ($query->rowCount() == 0) {
		$query = $db->prepare("INSERT INTO `users` (`username`, `password`) VALUES (:username, :password)");
		$result = $query->execute([
			':username' => $username,
			':password' => $password_hash,
		]);
		
		if ($result) {
			echo '<p class="success">Your registration was successful!</p>';
			header('Location: login');
		} else {
			echo '<p class="error">Something went wrong!</p>';
		}
	}
}
?>

	<div class="login-container">
		<div class="login-wrapper">
			<form class="login-form" method="POST">
				<span class="login-form-title">Sign Up</span>
				<div class="login-input-wrap">
					<span class="login-label">Username</span>
					<input class="login-input" type="text" name="username" placeholder="Enter your username" data-validate="Username is required" />
					<div class="focus-input" data-symbol="&#xf206;"></div>
				</div>
				<div class="login-input-wrap">
					<span class="login-label">Password</span>
					<input class="login-input" type="password" name="password" placeholder="Enter your password" data-validate="Password is required" />
					<div class="focus-input" data-symbol="&#xf190;"></div>
				</div>
				<div class="login-submit">
					<div class="login-submit-cont">
						<div class="login-submit-background"></div>
						<input class="login-submit-btn" type="submit" name="register" value="SIGN UP" />
					</div>
				</div>
				<span class="login-sign-up1">Or Log In Using</span>
				<span class="login-sign-up2"><a href="javascript:delay('login')">Log In</a></span>
			</form>
		</div>
	</div>
</body>
</html>
