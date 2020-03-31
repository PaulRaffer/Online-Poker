<html>
	<head>
		<script type="text/javascript" src="reload.js"></script>
	</head>

	<body>
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

		$query_get_game_id = $connection->prepare("SELECT `id` FROM `games` ORDER BY `id` DESC");
		$query_get_game_id->execute([]);

		$game_id = $query_get_game_id->fetch(PDO::FETCH_ASSOC)['id'];
		echo "Hallo".$game_id;

		header("Location: /poker/?game=$game_id");

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
		if (isset($_POST['set_action'])) {
			//$user_id = $_SESSION['user_id'];
			if (!isset($_POST['action'])) {
				echo "Bitte Aktion Auswählen!<br />";
			} else {
				//echo $_POST['action']."<br />";
				//echo "USER-ID: ".$user_id;

				$query_get_action_id = $connection->prepare("SELECT `id` FROM `actions` WHERE `name`=:action_name");
				$query_get_action_id->execute([
					':action_name' => $_POST['action'],
				]);
				$action_id = $query_get_action_id->fetch()['id'];

				$query_set_action = $connection->prepare("UPDATE `players` SET `last_action`=:last_action WHERE `user`=:user");
				$query_set_action->execute([
					':last_action' => $action_id,
					':user' => $user_id,
				]);
			}
		}



		$game_id = $_GET['game'];

		$query_in_game = $connection->prepare("SELECT * FROM `players` WHERE `user`=:user_id AND `game`=:game_id");
		$query_in_game->execute([
			':user_id' => $user_id,
			':game_id' => $game_id,
		]);
		if ($query_in_game->rowCount() == 0) {
			
			$query_num_players = $connection->prepare("SELECT * FROM `players` WHERE `game`=:game_id");
			$query_num_players->execute([
				':game_id' => $game_id,
			]);
			echo $query_num_players->rowCount();
			if ($query_num_players->rowCount() == 0) {
				$query_add_player = $connection->prepare("INSERT INTO `players` (`user`, `game`, `money`) VALUES (:user_id, :game_id, 1000)");
				$query_add_player->execute([
					':user_id' => $user_id,
					':game_id' => $game_id,
				]);
				$first_player = $connection->query("SELECT LAST_INSERT_ID()")->fetchColumn();

				$query_set_current_player = $connection->prepare("UPDATE `games` SET `current_player`=:first_player WHERE `id`=:game_id");
				$query_set_current_player->execute([
					':first_player' => $first_player,
					':game_id' => $game_id,
				]);
			} else {
				$query_first_player = $connection->prepare("SELECT `id` FROM `players` WHERE `game`=:game_id LIMIT 1");
				$query_first_player->execute([':game_id' => $game_id]);
				$first_player = $query_first_player->fetch(PDO::FETCH_ASSOC)["id"];

				$query_add_player = $connection->prepare("SELECT `id` FROM `players` WHERE `next_player`=:first_player");
				$query_add_player->execute([
					':first_player' => $first_player,
				]);
				$last_player = $query_add_player->fetch(PDO::FETCH_ASSOC)["id"];

				$query_add_player = $connection->prepare("UPDATE `players` SET `next_player`=NULL WHERE `next_player`=:first_player");
				$query_add_player->execute([
					':first_player' => $first_player,
				]);

				$query_add_player = $connection->prepare("INSERT INTO `players` (`user`, `game`, `next_player`, `money`) VALUES (:user_id, :game_id, :first_player, 1000)");
				$query_add_player->execute([
					':user_id' => $user_id,
					':game_id' => $game_id,
					':first_player' => $first_player,
				]);
				$new_player = $connection->query("SELECT LAST_INSERT_ID()")->fetchColumn();
				echo "!!! <".$new_player."> ???";

				$query_add_player = $connection->prepare("UPDATE `players` SET `next_player`=:new_player WHERE `next_player` IS NULL");
				$query_add_player->execute([
					':new_player' => $new_player,
					//':last_player' => $last_player,
				]);
	
				// TODO: next_player vom bisher letzten spieler aktualisieren
	
			}
		} 
		
		echo '<span id="players"></span>';

		$query = $connection->prepare("SELECT `users`.`id`, `username`, `money`, `card1`, `card2` FROM `players` JOIN `users` ON `players`.`user` = `users`.`id` WHERE `players`.`game`=:game_id");
		$query->execute([ ':game_id' => $game_id ]);

		while ($user = $query->fetch(PDO::FETCH_ASSOC))
		{
			$query_cards = $connection->prepare("SELECT `symbol` FROM `cards` WHERE `id`=:card1 OR `id`=:card2");
			$query_cards->execute([ ':card1' => $user['card1'], ':card2' => $user['card2'] ]);
			$cards = [$query_cards->fetch(PDO::FETCH_ASSOC)['symbol'], $query_cards->fetch(PDO::FETCH_ASSOC)['symbol']];

			if ($user['id'] == $user_id) {
				echo
				'<div><form method="post"><table>'.
				'<tr><td>Cards:</td><td  style="font-size: 80pt;">'.$cards[0].$cards[1].'</td></tr>'.
				'<tr><td>Check:</td><td><input type="radio" name="action" value="check" /></td>'.
				'<tr><td>Call:</td><td><input type="radio" name="action" value="call"  /></td>'.
				'<tr><td>Raise:</td><td><input type="radio" name="action" value="raise"  /></td>'.
				'<tr><td>Fold:</td><td><input type="radio" name="action" value="fold"  /></td>'.
				'<tr><td></td><td><input type="submit" name="set_action" /></td>'.
				'</table></form></div><br />';
			}
		}
	}
}
?>
	</body>
</html>
