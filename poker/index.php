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

	$small_blind = 25;
	$big_blind = 50;

	if (isset($_GET['game_name'])) {
		// NEUES SPIEL ERSTELLEN:
		$game_name = $_GET['game_name'];

		$query_create_game = $connection->prepare("INSERT INTO `games` (`name`) VALUES (:game_name)");
		$query_create_game->execute([':game_name' => $game_name]);

		$query_get_game_id = $connection->prepare("SELECT `id` FROM `games` ORDER BY `id` DESC");
		$query_get_game_id->execute([]);

		$game_id = $query_get_game_id->fetch(PDO::FETCH_ASSOC)['id'];
		//echo "Hallo".$game_id;

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
		while ($g = $query_game->fetch(PDO::FETCH_ASSOC))
		{
			echo '<tr><td>'.$g['id'].'</td><td><a href="http://127.0.0.1/poker/?game='.$g['id'].'">'.$g['name'].'</a></td></tr>';
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

		$query_player = $connection->prepare("SELECT * FROM `players` WHERE `user`=:user_id AND `game`=:game_id");
		$query_player->execute([
			':user_id' => $user_id,
			':game_id' => $game_id,
		]);

		$player = $query_player->fetch(PDO::FETCH_ASSOC);
		$player_id = $player['id'];

		if (isset($_POST['set_action'])) {
			//$user_id = $_SESSION['user_id'];
			if (!isset($_POST['action'])) {
				echo "Bitte Aktion Auswählen!<br />";
			} else {
				//echo $_POST['action']."<br />";
				//echo "USER-ID: ".$user_id;
				

				$query_current_player = $connection->prepare("SELECT `current_player` FROM `games` WHERE `id`=:game_id");
				$query_current_player->execute([
					':game_id' => $game_id,
				]);
				$current_player = $query_current_player->fetch(PDO::FETCH_ASSOC)['current_player'];
				
				if ($player_id != $current_player) {
					echo '<span class="error">Du bist nicht dran!</span><br />';
				} else {
					// LAST ACTION AKTUALISIEREN:
					$query_get_action_id = $connection->prepare("SELECT `id` FROM `actions` WHERE `name`=:action_name");
					$query_get_action_id->execute([
						':action_name' => $_POST['action'],
					]);
					$action_id = $query_get_action_id->fetch(PDO::FETCH_ASSOC)['id'];

					$query_set_action = $connection->prepare("UPDATE `players` SET `last_action`=:last_action WHERE `id`=:player_id");
					$query_set_action->execute([
						':last_action' => $action_id,
						':player_id' => $player_id,
					]);

					// NÄCHSTER SPIELER IST DRAN:
					
					$query_get_next_player = $connection->prepare("SELECT `next_player` FROM `players` WHERE `id`=:player_id");
					$query_get_next_player->execute([
						':player_id' => $player_id,
					]);

					$next_player = $query_get_next_player->fetch()['next_player'];
					//echo "...".$next_player."...";
					$query_set_next_player = $connection->prepare("UPDATE `games` SET `current_player`=:next_player WHERE `id`=:game_id");
					$query_set_next_player->execute([
						':next_player' => $next_player,
						':game_id' => $game_id,
					]);
				}
			}
		}

		$query_players = $connection->prepare("SELECT * FROM `players` WHERE `game`=:game_id");
		$query_players->execute([
			':game_id' => $game_id,
		]);
		
		if ($query_player->rowCount() == 0) {
			// WENN MAN IM SPIEL IST:
			
			//echo $query_players->rowCount();
			if ($query_players->rowCount() == 0) {
				// WENN NOCH KEINE SPIELER IM SPIEL SIND:
				// SPIELER HINZUFÜGEN:
				$query_add_player = $connection->prepare("INSERT INTO `players` (`user`, `game`, `money`) VALUES (:user_id, :game_id, 1000)");
				$query_add_player->execute([
					':user_id' => $user_id,
					':game_id' => $game_id,
				]);
				$first_player = $connection->query("SELECT LAST_INSERT_ID()")->fetchColumn();

				// ERSTER SPIELER IST DEALER:
				$query_set_dealer = $connection->prepare("UPDATE `games` SET `dealer`=:first_player WHERE `id`=:game_id");
				$query_set_dealer->execute([
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

				$query_add_player = $connection->prepare("UPDATE `players` SET `next_player`=:new_player WHERE `next_player` IS NULL AND `game`=:game_id");
				$query_add_player->execute([
					':new_player' => $new_player,
					':game_id' => $game_id,
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
				'<tr><td>Cards:</td><td style="font-size: 80pt;">'.$cards[0].$cards[1].'</td></tr>'.
				'<tr><td>Check:</td><td><input type="radio" name="action" value="check" /></td>'.
				'<tr><td>Call:</td><td><input type="radio" name="action" value="call"  /></td>'.
				'<tr><td>Raise:</td><td><input type="radio" name="action" value="raise"  /></td>'.
				'<tr><td>Fold:</td><td><input type="radio" name="action" value="fold"  /></td>'.
				'<tr><td></td><td><input type="submit" name="set_action" /></td>'.
				'</table></form></div><br />';
			}
		}





		if (isset($_GET['cards'])) {
			$query_game = $connection->prepare("SELECT `phase`, `dealer` FROM `games` WHERE `id`=:game_id");
			$query_game->execute([':game_id' => $game_id,]);
			$game = $query_game->fetch(PDO::FETCH_ASSOC);
			$dealer_id = $game['dealer'];

			echo "((($player_id---$dealer_id)))";
			
			if ($player_id == $dealer_id && $game['phase'] == 1) {
				$cards = range(1, 52);
				shuffle($cards);
				foreach ($cards as $card) {
					echo "$card ";
				}
				
				$card_top = -1;
				// KARTEN AUSTEILEN:
				while ($p = $query_players->fetch())
				{
					//echo "<br>". $card_top .": ".$cards[$card_top+=1];// $card_top+=1;
					//echo ", ".$cards[$card_top+=1];// $card_top+=1;

					$query_cards = $connection->prepare("UPDATE `players` SET `card1`=:card1, `card2`=:card2 WHERE `id`=:player_id");
					$query_cards->execute([
						':player_id' => $p['id'],
						':card1' => $cards[$card_top+=1],
						':card2' => $cards[$card_top+=1],
					]);
					
				}

				// KARTEN AUF DEN TISCH LEGEN:
				$query_cards = $connection->prepare("UPDATE `games` SET `card1`=:card1, `card2`=:card2, `card3`=:card3, `card4`=:card4, `card5`=:card5 WHERE `id`=:game_id");
				$query_cards->execute([
					':game_id' => $game_id,
					':card1' => $cards[$card_top+=1],
					':card2' => $cards[$card_top+=1],
					':card3' => $cards[$card_top+=1],
					':card4' => $cards[$card_top+=1],
					':card5' => $cards[$card_top+=1],
				]);

				// BLINDS:
				$small_blind_player_id = $player['next_player'];

				$query_small_blind_player = $connection->prepare("SELECT `next_player`, `money` FROM `players` WHERE `id`=:small_blind_id");
				$query_small_blind_player->execute([
					':small_blind_id' => $small_blind_player_id,
				]);
				$small_blind_player = $query_small_blind_player->fetch();
				$big_blind_player_id = $small_blind_player['next_player'];

				$query_big_blind_player = $connection->prepare("SELECT `next_player`, `money` FROM `players` WHERE `id`=:big_blind_id");
				$query_big_blind_player->execute([
					':big_blind_id' => $big_blind_player_id,
				]);
				$big_blind_player = $query_big_blind_player->fetch();
				$start_player_id = $big_blind_player['next_player'];

				// SMALL BLIND ABZIEHEN:
				if ($small_blind_player['money'] >= $small_blind) {
					$small_blind_player_new_money = $small_blind_player['money'] - $small_blind;
				} else {
					$small_blind_player_new_money = 0;
				}
				$query_set_small_blind_player_money = $connection->prepare("UPDATE `players` SET `money`=:small_blind_player_new_money WHERE `id`=:small_blind_player_id");
				$query_set_small_blind_player_money->execute([
					':small_blind_player_id' => $small_blind_player_id,
					':small_blind_player_new_money' => $small_blind_player_new_money,
				]);

				// BIG BLIND ABZIEHEN:
				if ($big_blind_player['money'] >= $big_blind) {
					$big_blind_player_new_money = $big_blind_player['money'] - $big_blind;
				} else {
					$big_blind_player_new_money = 0;
				}
				$query_set_big_blind_player_money = $connection->prepare("UPDATE `players` SET `money`=:big_blind_player_new_money WHERE `id`=:big_blind_player_id");
				$query_set_big_blind_player_money->execute([
					':big_blind_player_id' => $big_blind_player_id,
					':big_blind_player_new_money' => $big_blind_player_new_money,
				]);

				// SPIELER LINKS VOM BIG BLIND IS DRAN:
				

				$player = $query_player->fetch(PDO::FETCH_ASSOC);

				$query_set_current_player = $connection->prepare("UPDATE `games` SET `current_player`=:next_player WHERE `id`=:game_id");
				$query_set_current_player->execute([
					':next_player' => $player['next_player'],
					':game_id' => $game_id,
				]);
			}
		}



	}
}
?>
	</body>
</html>
