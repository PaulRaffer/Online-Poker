<?php

include($_SERVER['DOCUMENT_ROOT'].'/config.php');
session_start();

if (isset($_POST['login']) && $_SERVER["REQUEST_METHOD"] == "POST") {
	
	$username = $_POST['username'];
	$password = $_POST['password'];
	
	$query = $connection->prepare("SELECT * FROM `users` WHERE `username`=:username");
	$query->execute([ ':username' => $username ]);
	
	$user = $query->fetch(PDO::FETCH_ASSOC);
	
	if ($user && password_verify($password, $user['password'])) {
		$_SESSION['user_id'] = $user['id'];
		echo "<p class=\"success\">Hello $username, you are logged in!</p>";
		header('Location: /poker/join_game/');
	} else {
		echo '<p class="error">Wrong username or password!</p>';
	}
}

?>



<h2>Login</h2>
<form method="post">
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
	<input type="submit" name="login" value="Login" />
</form>
