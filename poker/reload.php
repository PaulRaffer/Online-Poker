<?php

header("Content-Type: text/event-stream");
header("Cheche-Control: no-cache");

include($_SERVER['DOCUMENT_ROOT'].'/config.php');
include('poker_hand.php');
include('constants.php');

session_start();

if (!isset($_SESSION['user_id'])) {
	header('Location: /login/');
	exit;
}

$str = "";

$user_id = $_SESSION['user_id'];
$game_id = $_GET['game'];
	
$query_player = $db->prepare("SELECT `id` FROM `players` WHERE `user`=:user_id AND `game`=:game_id");
$query_player->execute([
	':user_id' => $user_id,
	':game_id' => $game_id,
]);
$player = $query_player->fetch(PDO::FETCH_ASSOC);
$player_id = $player['id'];

$query_players_users = $db->prepare("SELECT `players`.`id`, `username`, `money`, `bet`, `card1`, `card2`, `last_action` FROM `players` JOIN `users` ON `players`.`user` = `users`.`id` WHERE `players`.`game`=:game_id ORDER BY `players`.`id`");
$query_players_users->execute([ ':game_id' => $game_id, ]);
		
$query_game = $db->prepare("SELECT * FROM `games` WHERE `id`=:game_id");
$query_game->execute([ ':game_id' => $game_id, ]);
$game = $query_game->fetch();

$str .=
	'<div class="game"><div class="game_info"><table>'.
	'<tr><td>Game-ID:</td><td>'.$game_id.'</td></tr>'.
	'<tr><td>Anzahl der Spieler:</td><td>'.$query_players_users->rowCount().'</td></tr>'.
	"<tr><td>Session User ID:</td><td>$user_id</td></tr>".
	'<tr><td>Pot:</td><td><span class="money">'.$game['pot_money'].'$</span></td></tr>'.
	'<tr><td>Phase:</td><td>'.phase_num_to_str[$game['phase']].'</td></tr>'.
	'</table></div>';

// KARTEN AM TISCH ANZEIGEN:
$str .= '<div class="game_cards">';

$game_card_ids = [];

if ($game['phase'] >= flop) for ($c = 1; $c <= 3; ++$c) {
	$query_card = $db->prepare("SELECT `symbol`, `suit` FROM `cards` WHERE `id`=:card$c");
	$query_card->execute([ ":card$c" => $game["card$c"], ]);
	$card = $query_card->fetch(PDO::FETCH_ASSOC);
	$str .= '<span class="'.$card['suit'].'">'.$card['symbol'].'</span>';
	$game_card_ids += [ ":game_card{$c}_id" => $game["card$c"], ];
} else for ($c = 1; $c <= 3; ++$c) $str .= '<span>🂠</span>';
		
if ($game['phase']  >= turn) {
	$query_card = $db->prepare("SELECT `symbol`, `suit` FROM `cards` WHERE `id`=:card4");
	$query_card->execute([ ":card4" => $game["card4"], ]);
	$card = $query_card->fetch(PDO::FETCH_ASSOC);
	$str .= '<span class="'.$card['suit'].'">'.$card['symbol'].'</span>';
	$game_card_ids += [ ":game_card4_id" => $game["card4"], ];
} else $str .= '<span>🂠</span>';
		
if ($game['phase']  >= river) {
	$query_card = $db->prepare("SELECT `symbol`, `suit` FROM `cards` WHERE `id`=:card5");
	$query_card->execute([ ":card5" => $game["card5"], ]);
	$card = $query_card->fetch(PDO::FETCH_ASSOC);
	$str .= '<span class="'.$card['suit'].'">'.$card['symbol'].'</span>';
	$game_card_ids += [ ":game_card5_id" => $game["card5"], ];
} else $str .= '<span>🂠</span>';

$str .= '</div></div>';

while ($pu = $query_players_users->fetch(PDO::FETCH_ASSOC)) {
	$player_card_ids = [
		":player_card1_id" => $pu["card1"],
		":player_card2_id" => $pu["card2"],
	];
	$player_card_ids += $game_card_ids;

	$query_player_cards = $db->prepare("SELECT `id`, `symbol`, `suit`, `rank` FROM `cards` WHERE `id`=:player_card1_id OR `id`=:player_card2_id"
		.($game['phase'] >= flop  ? " OR `id`=:game_card1_id OR `id`=:game_card2_id OR `id`=:game_card3_id" : "")
		.($game['phase'] >= turn  ? " OR `id`=:game_card4_id" : "")
		.($game['phase'] >= river ? " OR `id`=:game_card5_id" : ""));
		
	$query_player_cards->execute($player_card_ids);

	$player_cards = array();
	$cards = [];
	while ($c = $query_player_cards->fetch(PDO::FETCH_ASSOC)) {
		array_push($cards, $c);
		if ($c['id'] == $pu["card1"] || $c['id'] == $pu["card2"])
			array_push($player_cards, $c);
	}

	$hand_rank = poker_hand($cards);
	$hand = hand_num_to_str[$hand_rank[0]];
	$hand .= ' <span class="rank">(';
	for ($i = 1; $i < count($hand_rank); $i += 1)
		$hand .= rank_num_to_char[$hand_rank[$i]];
	$hand .= ')</span>';

	if (debug) var_dump($player_cards);
	
	$is_you = $pu['id'] == $player_id;
	$is_dealer = $pu['id'] == $game['dealer'];
	$is_current_player = $pu['id'] == $game['current_player'] && $game['phase'] != showdown+1;

	$str .=
		'<div class="player'.
		($is_you ? ' you' : '').
		($is_dealer ? ' dealer' : '').
		($is_current_player ? ' current_player' : '').
		($pu['last_action'] == fold ? ' fold' : '').
		'"><div class="player_info"><table>'.
		'<tr><td>Username:</td><td>'.$pu['username'].'</td></tr>'.
		'<tr><td>Money:</td><td><span class="money">'.$pu['money'].'$</span></td></tr>'.
		'<tr><td>Bet:</td><td><span class="money">'.$pu['bet'].'$</span></td></tr>'.
		'<tr><td>Hand:</td><td>'.
		($pu['id'] == $player_id && $game['phase'] >= preflop
		|| $game['phase'] >= showdown && $pu['last_action'] != fold
		? $hand : '').
		'</td></tr>'.
		'<tr><td>Last Action:</td><td>'.action_num_to_str[$pu['last_action']].'</td></tr>'.
		'</table></div><div class="player_cards">'.
		($pu['id'] == $player_id && $game['phase'] >= preflop
		|| $game['phase'] >= showdown && $pu['last_action'] != fold
		? '<span class="'.$player_cards[0]['suit'].'">'.$player_cards[0]['symbol'].'</span>'.
		  '<span class="'.$player_cards[1]['suit'].'">'.$player_cards[1]['symbol'].'</span>'
		: '<span>🂠</span><span>🂠</span>').
		'</div><div class="player_right">'.
		'<div class="player_dealer_button"><span>'.($is_dealer ? 'Ⓓ' : '').'</span></div>'.
		'<div class="player_you"><span>'.($is_you ? 'you' : '').'</span></div>'.
		'<div class="player_current_player"><span>'.($is_current_player ? ($is_you ? 'your turn' : 'thinking...') : '').'</span></div>'.
		'</div></div>';
}
echo "retry: 50\ndata: $str\n\n";
flush();
