<?php

include($_SERVER['DOCUMENT_ROOT'].'/config.php');
session_start();

if (!isset($_SESSION['user_id'])) {
	header('Location: /login/');
	exit;
} else {
	$user_id = $_SESSION['user_id'];

	if (isset($_GET['game_name'])) {
		$game_name = $_GET['game_name'];

		$query_create_game = $connection->prepare("INSERT INTO `games` (`name`) VALUES (:game_name)");
		$query_create_game->execute([':game_name' => $game_name]);
	} else if (!isset($_GET['game'])) { ?>
		<h2>Join game<h2>
		<form>
			<table>
				<tr><td>Game-ID:</td><td><input name="game" /></td><td><input type="submit" value="Join game" /></td></tr>
			</table>
		</form>
	<?php
		$query_game = $connection->prepare("SELECT `id`, `name` FROM `games`");
		$query_game->execute([]);
		
		echo '<table><tr><th>Game-ID</th><th>Name</th></tr>';
		while ($game = $query_game->fetch(PDO::FETCH_ASSOC))
		{
			echo '<tr><td>'.$game['id'].'</td><td><a href="http://127.0.0.1/poker/?game='.$game['id'].'">'.$game['name'].'</a></td></tr>';
		}
	?>
		</table>
		
		<h2>Create game</h2>
		<form>
			<table>
				<tr><td>Name:</td><td><input name="game_name" /></td><td><input type="submit" value="Create game" /></td></tr>
			</table>
		</form>
			
	
	<?php
	} else {
		$game_id = $_GET['game'];

		$query_num_players = $connection->prepare("SELECT * FROM `players` WHERE `user`=:user_id AND `game`=:game_id");
		$query_num_players->execute([
			':user_id' => $user_id,
			':game_id' => $game_id
		]);
		if ($query_num_players->rowCount() == 0) {
			$query_add_player = $connection->prepare("INSERT INTO `players` (`user`, `game`, `money`) VALUES (:user_id, :game_id, 1000)");
			$query_add_player->execute([
				':user_id' => $user_id,
				':game_id' => $game_id
			]);
		}
		
		$query = $connection->prepare("SELECT `users`.`id`, `username`, `money`, `card1`, `card2` FROM `players` JOIN `users` ON `players`.`user` = `users`.`id` WHERE `players`.`game`=:game_id");
		$query->execute([ ':game_id' => $game_id ]);
		
		echo
			'Anzahl der Spieler: '.$query->rowCount().'<br />'.
			'Session User ID: '.$user_id;
		//echo '<aside>';
		while ($user = $query->fetch(PDO::FETCH_ASSOC))
		{
			$query_cards = $connection->prepare("SELECT `symbol` FROM `cards` WHERE `id`=:card1 OR `id`=:card2");
			$query_cards->execute([ ':card1' => $user['card1'], ':card2' => $user['card2'] ]);
			$cards = [$query_cards->fetch(PDO::FETCH_ASSOC)['symbol'], $query_cards->fetch(PDO::FETCH_ASSOC)['symbol']];

			echo
				'<div><table>'.
					'<tr><td>User ID:</td><td>'.$user['id'].'</td></tr>'.
					'<tr><td>Username:</td><td>'.$user['username'].'</td></tr>'.
					'<tr><td>Money:</td><td>'.$user['money'].'</td></tr>'.
					($user['id'] == $user_id ? '<tr><td>Cards:</td><td  style="font-size: 80pt;">'.$cards[0].$cards[1].'</td></tr>' : '').
				'</table></div><br />';
		}
		//echo '</ol>';
		
	}
}
