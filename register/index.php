<?php

include($_SERVER['DOCUMENT_ROOT'].'/config.php');
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
			header('Location: /login/');
		} else {
			echo '<p class="error">Something went wrong!</p>';
		}
	}
}

?>


<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	</head>

	<body>
		<h2>Register</h2>
		<form method="post">
			<div>
				<table>
					<tr>
						<td><label>Username:</label></td>
						<td><input type="text" name="username" pattern="[a-zA-Z0-9]+" placeholder="username" required /></td>
					</tr>
					<tr>
						<td><label>Password:</label></td>
						<td><input type="password" name="password"  placeholder="password" /></td>
					</tr>
				</table>
				<input type="submit" name="register" value="Register" />
			</div>
			<div><a href="../login">Login</a></div>
		</form>
	</body>
</html>
