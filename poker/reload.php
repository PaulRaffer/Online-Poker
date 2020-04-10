<?php

header("Content-Type: text/event-stream");
header("Cheche-Control: no-cache");

include($_SERVER['DOCUMENT_ROOT'].'/config.php');
session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
	header('Location: /login/');
	exit;
} else {
	$str = "";
    
    $game_id = $_GET['game'];

        $query = $connection->prepare("SELECT `users`.`id`, `username`, `players`.`id`, `next_player`, `money`, `card1`, `card2` FROM `players` JOIN `users` ON `players`.`user` = `users`.`id` WHERE `players`.`game`=:game_id");
		$query->execute([ ':game_id' => $game_id ]);
		
		$query_game = $connection->prepare("SELECT `phase`, `card1`, `card2`, `card3`, `card4`, `card5` FROM `games` WHERE `id`=:game_id");
		$query_game->execute([ ':game_id' => $game_id ]);
		$game = $query_game->fetch();

		// KARTEN AM TISCH ANZEIGEN:
		$str .= '<span style="font-size: 80pt;">';

		if ($game['phase'] >= 2)
			for ($c = 1; $c <= 3; $c += 1) {
				$query_cards = $connection->prepare("SELECT `symbol` FROM `cards` WHERE `id`=:card$c");
				$query_cards->execute([ ":card$c" => $game["card$c"]]);
				$str .= $query_cards->fetch(PDO::FETCH_ASSOC)['symbol'];
			} else for ($c = 1; $c <= 3; $c += 1) { $str .= '🂠'; }
		
		if ($game['phase']  >= 3) {
			$query_cards = $connection->prepare("SELECT `symbol` FROM `cards` WHERE `id`=:card4");
			$query_cards->execute([ ":card4" => $game["card4"]]);
			$str .= $query_cards->fetch(PDO::FETCH_ASSOC)['symbol'];
		} else $str .= '🂠';
		
		if ($game['phase']  >= 4) {
			$query_cards = $connection->prepare("SELECT `symbol` FROM `cards` WHERE `id`=:card5");
			$query_cards->execute([ ":card5" => $game["card5"]]);
			$str .= $query_cards->fetch(PDO::FETCH_ASSOC)['symbol'];
		} else $str .= '🂠';

		$str .= '</span><br />';

		$str .=
			'Game-ID: '.$game_id.'<br />'.
			'Anzahl der Spieler: '.$query->rowCount().'<br />'.
			'Session User ID: '.$user_id;

		while ($user = $query->fetch(PDO::FETCH_ASSOC))
		{
			if ($user['id'] != $user_id) {
				$str .=
                	'<div><table>'.
						'<tr><td>User ID:</td><td>'.$user['id'].'</td></tr>'.
						'<tr><td>Username:</td><td>'.$user['username'].'</td></tr>'.
						'<tr><td>Money:</td><td>'.$user['money'].'</td></tr>';
					
				$str .= '</table></div><br />';
			}
        }
}
echo "retry: 50\ndata: $str\n\n";
flush();
