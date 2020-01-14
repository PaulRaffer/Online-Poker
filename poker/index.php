<?php

include($_SERVER['DOCUMENT_ROOT'].'/config.php');
session_start();

if (!isset($_SESSION['user_id'])) {
	header('Location: /login/');
	exit;
} else {



	if (!isset($_GET['game'])) {
	
	} else {
		$user_id = $_SESSION['user_id'];
		$game_id = $_GET['game'];
		
		$query = $connection->prepare("SELECT `username` FROM `players` JOIN `users` ON `players`.`user` = `users`.`id` WHERE `players`.`game`=:game_id");
		$query->execute([ ':game_id' => $game_id ]);
		
		while ($user = $query->fetch(PDO::FETCH_ASSOC))
		{
			echo $user['username'];
		}
	}


}


