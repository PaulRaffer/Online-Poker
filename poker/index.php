<?php

function print_debug($game) {
	echo
		'<table class="debug">'.
		'<tr><td>PHASE:</td><td>'.$game['phase'].'</td></tr>'.
		'<tr><td>CURRENT PLAYER:</td><td>'.$game['current_player'].'</td></tr>'.
		'</table>';
}

function get_game($db, $game_id) { // Spiel von databank einlesen
	$query = $db->prepare('SELECT * FROM `games` WHERE `id`=:game_id');
	$query->execute([ ':game_id' => $game_id, ]);
	return $query->fetch(PDO::FETCH_ASSOC);
}

function set_game($db, $game) { // Spiel auf datenbank schreiben
	$query = $db->prepare('UPDATE `games` SET `dealer`=:dealer_id, `card1`=:card1_id, `card2`=:card2_id, `card3`=:card3_id, `card4`=:card4_id, `card5`=:card5_id, `phase`=:phase, `current_player`=:current_player_id, `pot_money`=:pot_money, `highest_bet`=:highest_bet, `highest_bet_player`=:highest_bet_player, `highest_raise`=:highest_raise WHERE `id`=:game_id');
	$query->execute([
		':game_id' => $game['id'],
		':dealer_id' => $game['dealer'],
		':card1_id' => $game['card1'],
		':card2_id' => $game['card2'],
		':card3_id' => $game['card3'],
		':card4_id' => $game['card4'],
		':card5_id' => $game['card5'],
		':phase' => $game['phase'],
		':current_player_id' => $game['current_player'],
		':pot_money' => $game['pot_money'],
		':highest_bet' => $game['highest_bet'],
		':highest_bet_player' => $game['highest_bet_player'],
		':highest_raise' => $game['highest_raise'],
	]);
}

function print_game($you, $game) { // Spiel ausgeben ?>
	<span id="players"></span>
	<div class="you action">
		<form method="post">
			<?php if ($you['id'] == $game['dealer'] && $game['phase'] == before_start) { ?>
				<input type="submit" value="START GAME" name="start_game" />
			<?php } else { ?>
				<table>
					<tr><td><label for="check_call">Check/Call:</label></td><td><input type="radio" name="action" id="check_call" value="1" checked /></td><td></td></tr>
					<tr><td><label for="raise">Raise:</label></td><td><input type="radio" name="action" id="raise" value="2" /></td><td><input type="number" onfocus="raise_func()" name="raise_money" class="money" /><span class="money">$</span></td></tr>
					<tr><td><label for="fold">Fold:</label></td><td><input type="radio" name="action" id="fold" value="3" /></td><td></td></tr>
				</table>
				<input type="submit" name="set_action" />
			<?php } ?>
		</form>
		<span>If you find a bug, please create an <a href="https://github.com/PaulRaffer/Online-Poker/issues" target="_blank">issue</a> on <a href="https://github.com/PaulRaffer/Online-Poker" target="_blank">GitHub</a>!</span>
	</div>
<?php }

function get_you($db, $user_id, $game_id) { // Eigenen Spieler von Datanbank lesen
	$query = $db->prepare('SELECT * FROM `players` WHERE `user`=:user_id AND `game`=:game_id');
	$query->execute([
		':user_id' => $user_id,
		':game_id' => $game_id,
	]);
	return $query->fetch(PDO::FETCH_ASSOC);
}

function add_player($db, $user_id, $game_id, $next_player_id = NULL, $start_money = 10000) { // Spieler zu Spiel hinzufügen
	$query = $db->prepare('INSERT INTO `players` (`user`, `game`, `next_player`, `money`, `last_action`) VALUES (:user_id, :game_id, :next_player_id, :start_money, :fold)');
	$query->execute([
		':user_id' => $user_id,
		':game_id' => $game_id,
		':next_player_id' => $next_player_id,
		':start_money' => $start_money,
		':fold' => fold,
	]);
}

function get_first_player($db, $game) { // Spieler mit niedrigster ID im Spiel lesen 
	$query = $db->prepare('SELECT `id` FROM `players` WHERE `game`=:game_id LIMIT 1');
	$query->execute([':game_id' => $game['id']]);
	return $query->fetch(PDO::FETCH_ASSOC);
}

function get_dealer($db, $game) {
	$query = $db->prepare('SELECT `next_player` FROM `players` WHERE `id`=:dealer_id');
	$query->execute([ ':dealer_id' => $game['dealer'], ]);
	return $query->fetch(PDO::FETCH_ASSOC);
}

function get_next_player($db, $player) { // Nächsten spieler von $player lesen (last_action=fold überspringen)
	do {
		$query = $db->prepare('SELECT `id`, `next_player`, `last_action` FROM `players` WHERE `id`=:next_player_id');
		$query->execute([ ':next_player_id' => $player['next_player'], ]);
		$player = $query->fetch(PDO::FETCH_ASSOC);
	} while ($player['last_action'] == fold);
	return $player;
}

function bet(&$game, &$player, $new_bet) { // Betrag bieten
	$bet_diff = $new_bet - $player['bet']; // wie viel fehlt noch auf neues Gebot?
	if ($player['money'] < $bet_diff)      // noch genug Geld?
		$bet_diff = $player['money'];      // sonst All-In

	$player['money'] -= $bet_diff;         // Spieler Geld abziehen
	$player['bet'] += $bet_diff;           // Gebot des Spielers erhöhen
	$game['pot_money'] += $bet_diff;       // Pot erhöhen
}

function get_winner_ids($db, $game) {
	$query_players_in_game = $db->prepare("SELECT `id`, `card1`, `card2` FROM `players` WHERE `game`=:game_id AND NOT `last_action`=:fold");
	$query_players_in_game->execute([
		':game_id' => $game['id'],
		':fold' => fold,
	]);

	// Gewinner ermitteln:
	$winner_ids = [];
	$winner_rank = [0, 0, 0, 0, 0, 0];
	while ($p = $query_players_in_game->fetch(PDO::FETCH_ASSOC)) {

		$query_card = $db->prepare("SELECT `rank`, `suit` FROM `cards` WHERE `id`=:player_card1_id OR `id`=:player_card2_id OR `id`=:game_card1_id OR `id`=:game_card2_id OR `id`=:game_card3_id OR `id`=:game_card4_id OR `id`=:game_card5_id");
		$query_card->execute([
			":player_card1_id" => $p['card1'],
			":player_card2_id" => $p['card2'],
			":game_card1_id" => $game['card1'],
			":game_card2_id" => $game['card2'],
			":game_card3_id" => $game['card3'],
			":game_card4_id" => $game['card4'],
			":game_card5_id" => $game['card5'],
		]);
		$cards = [];
		while ($c = $query_card->fetch(PDO::FETCH_ASSOC))
			array_push($cards, $c);
	
		$rank = poker_hand($cards); // Beste kartenkombination des Spielers herausfinden ...
		$cmp_rank = array_greater_recursive($rank, $winner_rank, count($rank));
		if ($cmp_rank == 1) { // ... und nachschauen ob sie besser ist als die vom bisher führenden
			$winner_rank = $rank; // bisher bester aktualisieren
			$winner_ids = [$p['id']];
		}
		else if ($cmp_rank == 0) { // nachschauen ob sie gleich gut ist
			array_push($winner_ids, $p['id']);
		}
	}
	return $winner_ids;
}

?>

<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		
		<link rel="stylesheet" type="text/css" href="../style/main.css" />
		<link rel="stylesheet" type="text/css" href="../style/light.css" title="Light" />
		<link rel="alternate stylesheet" type="text/css" href="../style/dark.css" title="Dark" />

		<script type="text/javascript" src="reload.js"></script>
		<script type="text/javascript" src="raise.js"></script>
	</head>

	<body>

<?php

include($_SERVER['DOCUMENT_ROOT'].'/config.php');
include('poker_hand.php');
include('array_greater_recursive.php');
include('constants.php');
session_start();

if (!isset($_SESSION['user_id'])) { // nicht eingeloggt:
	header('Location: /login/');    //  zu Login weiterleiten
	exit;
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['game_name'])) { // NEUES SPIEL ERSTELLEN:
	$query_create_game = $db->prepare('INSERT INTO `games` (`name`) VALUES (:game_name)');
	$query_create_game->execute([ ':game_name' => $_GET['game_name'], ]);
	$game_id = $db->query('SELECT LAST_INSERT_ID()')->fetchColumn();
	header("Location: /poker/?game=".$game_id); // zu neuem Spiel weiterleiten

} else if (!isset($_GET['game'])) { // Nicht in einem Spiel?
?>
	<h2>Create game</h2>
	<form><table>
		<tr><td>Name:</td><td><input name="game_name" /></td><td><input type="submit" value="Create game" /></td></tr>
	</table></form>

	<h2>Join game<h2><form><table>
		<tr><td>Game-ID:</td><td><input name="game" /></td><td><input type="submit" value="Join game" /></td></tr>
	</table></form>

	<table><tr><th>Game-ID</th><th>Name</th></tr>
<?php
	$query_games = $db->prepare("SELECT `id`, `name` FROM `games` ORDER BY `id` DESC");
	$query_games->execute([]);
	while ($g = $query_games->fetch(PDO::FETCH_ASSOC)) // Liste der Spiele ausgeben
		echo '<tr><td>'.$g['id'].'</td><td><a href="./?game='.$g['id'].'">'.$g['name'].'</a></td></tr>';
?>
	</table>
<?php
} else {
	$game = get_game($db, $_GET['game']); // Spiel mit id $_GET['game'] von db lesen
	$you = get_you($db, $user_id, $game['id']);

	$query_players = $db->prepare("SELECT * FROM `players` WHERE `game`=:game_id");
	$query_players->execute([ ':game_id' => $game['id'], ]);
	
	if (!$you) {                                                                    // WENN MAN NOCH NICHT IM SPIEL IST:
		if ($query_players->rowCount() == 0) {                                      //  WENN NOCH KEINE SPIELER IM SPIEL SIND:
			add_player($db, $user_id, $game['id']);                                 //   SPIELER HINZUFÜGEN
			$game['dealer'] = $db->query('SELECT LAST_INSERT_ID()')->fetchColumn(); //   ERSTER SPIELER IST DEALER
		} else {
			// Spieler als letzen Spieler einfügen:
			$first_player = get_first_player($db, $game); 

			$query_add_player = $db->prepare('UPDATE `players` SET `next_player`=NULL WHERE `next_player`=:first_player');
			$query_add_player->execute([ ':first_player' => $first_player['id'], ]);

			add_player($db, $user_id, $game['id'], $first_player['id']);
			$new_player['id'] = $db->query("SELECT LAST_INSERT_ID()")->fetchColumn();

			$query_add_player = $db->prepare('UPDATE `players` SET `next_player`=:new_player WHERE `next_player` IS NULL AND `game`=:game_id');
			$query_add_player->execute([
				':new_player' => $new_player['id'],
				':game_id' => $game['id'],
			]);
		}
		$you = get_you($db, $user_id, $game['id']);
	}
	
	$dealer = get_dealer($db, $game); // Dealer herausfinden

	$raised = false;
	$valid_action = false;
	if (isset($_POST['start_game'])) {
		$game['phase'] = dealing;
	} else if (isset($_POST['set_action'])) { // Action-Button geklickt
		if ($you['id'] != $game['current_player'] && $game['phase'] != showdown + 1) // nicht dran:
			echo '<span class="error">It\'s not your turn!</span><br />';
		else if (!isset($_POST['action'])) // keine Aktion ausgewählt:
			echo '<span class="error">Please select an action!</span><br />';
		else {
			$action = $_POST['action'];
			$valid_action = true;

			switch ($action)
			{
				case call:
					bet($game, $you, $game['highest_bet']); // mitgehen
				break;

				case raise:
					$raise_money = $_POST['raise_money'];
					$min_bet = $game['highest_bet'] + $game['highest_raise'];
					
					if ($raise_money >= $min_bet) { // genug erhöht?
						$game['highest_raise'] = $raise_money - $game['highest_bet']; // highest_raise aktualisieren
						bet($game, $you, $raise_money); // erhöhen

						$game['highest_bet'] = $you['bet']; // höchstes Gebot aktualisieren
						$game['highest_bet_player'] = $you['id'];

						$raised = true;
					} else {
						$valid_action = false; // nicht erlaupt, zu wenig erhöht
						echo '<span class="error">You have to raise at least to <span class="money">'.$min_bet.'$</span>!</span><br />';
					}
				break;

				case fold:
					$query_winner = $db->prepare("SELECT `id` FROM `players` WHERE `game`=:game_id AND NOT `last_action`=:fold AND NOT `id`=:you_id");
					$query_winner->execute([
						':game_id' => $game['id'],
						':fold' => fold,
						':you_id' => $you['id'],
					]);

					if ($query_winner->rowCount() == 1) { // Nur noch ein Spieler übrig => Gewonnen
						$winner = $query_winner->fetch(PDO::FETCH_ASSOC);
						
						// Gewinner erhält Pot:
						$query_set_winner_money = $db->prepare("UPDATE `players` SET `money`=`money`+:pot_money, `is_winner`=TRUE WHERE `id`=:winner_id");
						$query_set_winner_money->execute([ // Auf db schreiben
							':winner_id' => $winner['id'],
							':pot_money' => $game['pot_money'],
						]);

						$game['phase'] = dealing; // Neue Runde
					}
				break;
			}
			
			$you['last_action'] = $action; // LAST ACTION AKTUALISIEREN
		}
	}

	
	// deine daten in db aktualisieren:
	$query_set_you = $db->prepare('UPDATE `players` SET `money`=:you_money, `bet`=:you_bet, `last_action`=:last_action WHERE `id`=:you_id');
	$query_set_you->execute([
		':you_id' => $you['id'],
		':you_money' => $you['money'],
		':you_bet' => $you['bet'],
		':last_action' => $you['last_action'],
	]);



	$new_phase = false;

	$old_phase = $game['phase'];

	switch ($game['phase']) {
		case preflop:
			
			if (($you['id'] == $game['current_player'] && get_next_player($db, $you)['id'] == $game['highest_bet_player'] && $game['highest_bet'] != $game['big_blind_money'] || !$raised && $game['current_player'] == $game['highest_bet_player']) && $valid_action) {
			// (Du bist dran UND dein nächster Spieler hat am meisten Geboten UND das höchste Gebot ist nicht der big blind) ODER (es wurde nicht erhöht UND der Spieler der dran ist hat am meisten Geboten) UND die Aktion war gültig
				$game['current_player'] = get_next_player($db, $dealer)['id']; // Spieler links vom Dealer ist dran
				$game['highest_bet_player'] = $game['current_player']; // Damit zumindest jeder einmal dran kommt
				++$game['phase']; // nächste Phase
				$new_phase = true;
			}
		break;

		case flop:
		case turn:
		case river:
			if ($you['id'] == $game['current_player'] && get_next_player($db, $you)['id'] == $game['highest_bet_player'] && $valid_action) {
				$game['current_player'] = get_next_player($db, $dealer)['id'];
				$game['highest_bet_player'] = $game['current_player'];
				++$game['phase'];
				$new_phase = true;
			}

			if ($game['phase'] == showdown) {
				$winner_ids = get_winner_ids($db, $game);

				// set Gewinner in db:
				$query_set_winner_money = $db->prepare("UPDATE `players` SET `money`=`money`+:pot_money, `is_winner`=TRUE WHERE `id` IN (:winner_ids)");
				$query_set_winner_money->execute([
					':winner_ids' => implode(',', $winner_ids),
					':pot_money' => $game['pot_money'] / count($winner_ids),
				]);

				//$you['bet'] = 0; // Gebot zurücksetzen
				//$game['pot_money'] = 0; // Pot zurücksetzen


				echo '<script type="text/javascript" src="timer.js"></script>'; // start timer to next round
				++$game['phase'];
				$new_phase = true;
			}
		break;

		case dealing:

			$game['dealer'] = $dealer['next_player']; // Dealer-Button wird weitergegeben
			$dealer = get_dealer($db, $game);

			// Gebote auf 0$ zurücksetzen / noch kein Gewinner:
			$query_reset_bets = $db->prepare('UPDATE `players` SET `last_action`=:start_action, `bet`=0, `is_winner`=FALSE  WHERE `game`=:game_id');
			$query_reset_bets->execute([
				':game_id' => $game['id'],
				':start_action' => call,
			]);

			// Wer sind die Blinds?:
			// small blind:
			$small_blind_player_id = $dealer['next_player'];
			$query_small_blind_player = $db->prepare('SELECT `next_player`, `money`, `bet` FROM `players` WHERE `id`=:small_blind_id');
			$query_small_blind_player->execute([
				':small_blind_id' => $small_blind_player_id,
			]);
			$small_blind_player = $query_small_blind_player->fetch();

			// big blind:
			$big_blind_player_id = $small_blind_player['next_player'];
			$query_big_blind_player = $db->prepare('SELECT `next_player`, `money`, `bet` FROM `players` WHERE `id`=:big_blind_id');
			$query_big_blind_player->execute([
				':big_blind_id' => $big_blind_player_id,
			]);
			$big_blind_player = $query_big_blind_player->fetch();

			// Karten erstellen und mischen:
			$cards = range(1, 52);
			shuffle($cards);
				
			$card_top = 0;
			// Karten austeilen:
			while ($p = $query_players->fetch()) {
				$query_cards = $db->prepare("UPDATE `players` SET `card1`=:card1, `card2`=:card2 WHERE `id`=:player_id");
				$query_cards->execute([
					':player_id' => $p['id'],
					':card1' => $cards[$card_top++],
					':card2' => $cards[$card_top++],
				]);
			}

			// Karten auf den tisch legen:
			for ($c = 1; $c <= 5; ++$c)
				$game["card$c"] = $cards[$card_top++];

			// Pot leeren
			$game['pot_money'] = 0;

			bet($game, $small_blind_player, $game['small_blind_money']); // Small Blind abziehen
			bet($game, $big_blind_player, $game['big_blind_money']); // Big Blind abziehen

			// Small Blind in db aktualisieren:
			$query_set_small_blind_player_money = $db->prepare("UPDATE `players` SET `money`=:small_blind_player_money, `bet`=:small_blind_player_bet WHERE `id`=:small_blind_player_id");
			$query_set_small_blind_player_money->execute([
				':small_blind_player_id' => $small_blind_player_id,
				':small_blind_player_money' => $small_blind_player['money'],
				':small_blind_player_bet' => $small_blind_player['bet'],
			]);
			// Big Blind in db aktualisieren:
			$query_set_big_blind_player_money = $db->prepare("UPDATE `players` SET `money`=:big_blind_player_money, `bet`=:big_blind_player_bet WHERE `id`=:big_blind_player_id");
			$query_set_big_blind_player_money->execute([
				':big_blind_player_id' => $big_blind_player_id,
				':big_blind_player_money' => $big_blind_player['money'],
				':big_blind_player_bet' => $big_blind_player['bet'],
			]);

			// Höchstes Gebot = Big Blind:
			$game['highest_bet'] = $game['big_blind_money'];
			$game['highest_bet_player'] = $big_blind_player_id;
			$game['highest_raise'] = $game['big_blind_money'];

			$game['current_player'] = $big_blind_player['next_player']; // Spieler links vom big blind beginnt die Runde

			$game['phase'] = preflop; // nächste Phase
		break;
	}

	if (!$new_phase && $old_phase != dealing && $you['id'] == $game['current_player'] && $valid_action)
		$game['current_player'] = get_next_player($db, $you)['id']; // nächster Spieler ist dran, außer wenn eine neue Phase gestartet hat, man nicht dran ist oder die Aktion ungültig war.
	
	print_game($you, $game); // Ausgabe
	set_game($db, $game); // Zurück auf die db schreiben
}
?>
	</body>
</html>
