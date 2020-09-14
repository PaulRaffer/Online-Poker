<!DOCTYPE html>
<?php

include('config.php');
session_start();

if (isset($_POST['login']) && $_SERVER["REQUEST_METHOD"] == "POST") {
	
	$username = $_POST['username'];
	$password = $_POST['password'];
	
	$query = $db->prepare("SELECT * FROM `users` WHERE `username`=:username");
	$query->execute([ ':username' => $username ]);
	
	$user = $query->fetch(PDO::FETCH_ASSOC);
	
	if ($user && password_verify($password, $user['password'])) {
		$_SESSION['user_id'] = $user['id'];
		header('Location: /poker/index');
	} else {
		echo '<p class="error">Wrong username or password!</p>';
	}
}
?>

<html>
<head>
	<title> Poker | Login </title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<!--Stylesheets-->
	<link rel="stylesheet" type="text/css" href="style/dark.css" />
	<link rel="alternate stylesheet" type="text/css" href="style/light.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/font-awesome.min.css">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-design-iconic-font/2.2.0/css/material-design-iconic-font.min.css"> 
		
	<!--JavaScript-->
	<script defer src="https://use.fontawesome.com/releases/v5.0.4/js/all.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js" type="text/javascript"></script>
	<script src="js/script.js"></script>
</head>

<body>
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
						<input class="login-submit-btn" type="submit" name="login" value="LOGIN"/>
					</div>
				</div>
				<span class="login-sign-up1">Or Sign Up Using</span>
				<span class="login-sign-up2"><a href="javascript:delay('sign_up')">Sign Up</a></span>
			</form>
		</div>
	</div>
</body>
</html>
