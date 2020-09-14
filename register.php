<?php

include "config.php";
session_start();

if (isset($_POST['register']) && $_SERVER["REQUEST_METHOD"] == "POST") {
	
	$username = $_POST['username'];
	$password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
	
	$query = $db->prepare("SELECT * FROM `users` WHERE `username`=:username");
	$query->execute([ ':username' => $username ]);
	
	if ($query->rowCount() > 0) {
		echo "<p class=\"error\">Username '$username' already existes!</p>";
	} elseif ($query->rowCount() == 0) {
		$query = $db->prepare("INSERT INTO `users` (`username`, `password`) VALUES (:username, :password)");
		$result = $query->execute([
			':username' => $username,
			':password' => $password_hash
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


<html>
<head>
	<title> Poker | Register </title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<!--Stylesheets-->
	<link rel="stylesheet" type="text/css" href="../style/dark.css" />
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
				<span class="login-form-title">Sign Up</span>
				<div class="login-input-wrap">
					<span class="login-label">Username</span>
					<input class="login-input" type="text" name="username" placeholder="Enter your username" data-validate="Username is required">
					<div class="focus-input" data-symbol="&#xf206;"></div>
				</div>
				<div class="login-input-wrap">
					<span class="login-label">Password</span>
					<input class="login-input" type="password" name="password" placeholder="Enter your password" data-validate="Password is required">
					<div class="focus-input" data-symbol="&#xf190;"></div>
				</div>
				<div class="login-submit">
					<div class="login-submit-cont">
						<div class="login-submit-background"></div>
						<input class="login-submit-btn" type="submit" name="login" value="SIGN UP"/>
					</div>
				</div>
				<span class="login-sign-up1">Or Log In Using</span>
				<span class="login-sign-up2"><a href="javascript:delay('login')">Log In</a></span>
			</form>
		</div>
	</div>
</body>
</html>
