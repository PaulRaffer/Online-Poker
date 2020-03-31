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

    
    $game_id = $_GET['game'];

        $query = $connection->prepare("SELECT `users`.`id`, `username`, `players`.`id`, `next_player`, `money`, `card1`, `card2` FROM `players` JOIN `users` ON `players`.`user` = `users`.`id` WHERE `players`.`game`=:game_id");
		$query->execute([ ':game_id' => $game_id ]);
        

		$str =
			'Name des Spiels: '.'...'.'<br />'.
			'Anzahl der Spieler: '.$query->rowCount().'<br />'.
			'Session User ID: '.$user_id;
		//echo '<aside>';
		while ($user = $query->fetch(PDO::FETCH_ASSOC))
		{
			$query_cards = $connection->prepare("SELECT `symbol` FROM `cards` WHERE `id`=:card1 OR `id`=:card2");
			$query_cards->execute([ ':card1' => $user['card1'], ':card2' => $user['card2'] ]);
			$cards = [$query_cards->fetch(PDO::FETCH_ASSOC)['symbol'], $query_cards->fetch(PDO::FETCH_ASSOC)['symbol']];

            /*$str .=
                '<div><form action="set_action.php" method="post"><table>'.
					'<tr><td>User ID:</td><td>'.$user['id'].'</td></tr>'.
					'<tr><td>Username:</td><td>'.$user['username'].'</td></tr>'.
					'<tr><td>Money:</td><td>'.$user['money'].'</td></tr>';
					if ($user['id'] == $user_id) {
						$str .=
							'<tr><td>Cards:</td><td  style="font-size: 80pt;">'.$cards[0].$cards[1].'</td></tr>'.
							'<tr><td>Check:</td><td><input type="radio" name="action" value="check" /></td>'.
							'<tr><td>Call:</td><td><input type="radio" name="action" value="call"  /></td>'.
							'<tr><td>Raise:</td><td><input type="radio" name="action" value="raise"  /></td>'.
							'<tr><td>Fold:</td><td><input type="radio" name="action" value="fold"  /></td>'.
							'<tr><td></td><td><input type="submit" /></td>';
					}
			$str .= '</table></form></div><br />';*/

			if ($user['id'] != $user_id) {
				$str .=
                	'<div><form action="set_action.php" method="post"><table>'.
						'<tr><td>User ID:</td><td>'.$user['id'].'</td></tr>'.
						'<tr><td>Username:</td><td>'.$user['username'].'</td></tr>'.
						//'<tr><td>Player-ID:</td><td>'.$user['username'].'</td></tr>'.
						
						'<tr><td>Money:</td><td>'.$user['money'].'</td></tr>';
					
				$str .= '</table></form></div><br />';
			}
        }
}
echo "retry: 50\ndata: $str\n\n";
flush();
