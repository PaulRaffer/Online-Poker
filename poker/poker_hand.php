<?php

define("high_card", 0);
define("one_pair", 1);
define("two_pair", 2);
define("three_of_a_kind", 3);
define("straight", 4);
define("flush", 5);
define("full_house", 6);
define("four_of_a_kind", 7);
define("straight_flush", 8);

define("rank_char_to_num", [
	'2' =>  2,
	'3' =>  3,
	'4' =>  4,
	'5' =>  5,
	'6' =>  6,
	'7' =>  7,
	'8' =>  8,
	'9' =>  9,
	'T' => 10,
	'J' => 11,
	'Q' => 12,
	'K' => 13,
	'A' => 14,
]);

define("rank_num_to_char", [
     1 => 'A',
     2 => '2',
     3 => '3',
     4 => '4',
     5 => '5',
     6 => '6',
     7 => '7',
     8 => '8',
     9 => '9',
    10 => 'T',
    11 => 'J',
    12 => 'Q',
    13 => 'K',
    14 => 'A',
]);


define("hand_num_to_str", [
	high_card => 'High card',
	one_pair => 'One pair',
	two_pair => 'Two pair',
	three_of_a_kind => 'Three of a kind',
	straight => 'Straight',
	flush => 'Flush',
	full_house => 'Full house',
	four_of_a_kind => 'Four of a kind',
	straight_flush => 'Straight flush',

]);







function poker_hand($cards) { // ermittelt die Stärke einer Pokerhand
	foreach (['♠', '♥', '♦', '♣'] as $s)
		$suit_count[$s] = 0; // wie oft kommt jede Farbe vor?

	for ($r = 2; $r <= 14; $r += 1)
		$rank_count[$r] = 0; // Wie oft kommt jeder Wert vor?

	// Welche Frabe kommt am öftesten vor?:
	$max_suit_count = 0;
	foreach ($cards as $c) {
		$suit_count[$c['suit']] += 1;
		if ($suit_count[$c['suit']] > $max_suit_count) {
			$max_suit_count = $suit_count[$c['suit']];
			$max_suit = $c['suit'];
		}
		$rank_count[rank_char_to_num[$c['rank']]] += 1;
	}
	
	// Welcher Wert kommt am öftesten vor?:
	$max_rank[0] = 0;
	for ($i = 1; $i <= 5; $i += 1) {
		$max_rank[$i] = 0;
		$max_rank_count[$i] = 0;
		for ($r = 14; $r >= 2; $r -= 1)
			if ($rank_count[$r] > $max_rank_count[$i] && !in_array($r, array_slice($max_rank, 0, $i))) {
				$max_rank_count[$i] = $rank_count[$r];
				$max_rank[$i] = $r;
			}
	}

	/*echo 'MAX RANK1: '.$max_rank[1].'<br />MAX RANK COUNT1: '.$max_rank_count[1].'<br />';
	echo 'MAX RANK2: '.$max_rank[2].'<br />MAX RANK COUNT2: '.$max_rank_count[2].'<br />';
	echo 'MAX RANK3: '.$max_rank[3].'<br />MAX RANK COUNT3: '.$max_rank_count[3].'<br />';
	echo 'MAX RANK4: '.$max_rank[4].'<br />MAX RANK COUNT4: '.$max_rank_count[4].'<br />';
	*/



	foreach (['♠', '♥', '♦', '♣'] as $s) {
		$straight_flush_count[$s] = 0;
		$straight_flush_rank[$s] = 0;
		$old_rank[$s] = -1;
		$ace[$s] = 0;
	}
	
	foreach ($cards as $c) {
		if ($c['rank'] == 'A' && $old_rank[$c['suit']] == -1) {
			//echo "Hallo";
			$straight_flush_count[$c['suit']] = 1;
			$old_rank[$c['suit']] = 1;
		} else if (rank_char_to_num[$c['rank']] - 1 == $old_rank[$c['suit']] || $old_rank[$c['suit']] == -1) {
			//echo $old_rank.' ';
			$straight_flush_count[$c['suit']] += 1;
			//echo $c['rank'].$straight_flush_count[$c['suit']].' ';
			if ($straight_flush_count[$c['suit']] >= 5) {
				$straight_flush_rank[$c['suit']] = rank_char_to_num[$c['rank']];
			}
			$old_rank[$c['suit']] = rank_char_to_num[$c['rank']];
		} else {
			$straight_flush_count[$c['suit']] = 1;
			$old_rank[$c['suit']] = rank_char_to_num[$c['rank']];
		}
		
	}
	$straight_flush_max_rank = max($straight_flush_rank);
	if ($straight_flush_max_rank)
		return [straight_flush, $straight_flush_max_rank/*Wert der höchsten Karte im Straight Flush*/];
	
	if ($max_rank_count[1] == 4)
		return [four_of_a_kind, $max_rank[1]/*Wert des Vierlings*/, $max_rank[2]];

	if ($max_suit_count >= 5) {
		$flush_ranks = [];
		foreach ($cards as $c)
			if ($c['suit'] == $max_suit)
				array_push($flush_ranks, rank_char_to_num[$c['rank']]);
		rsort($flush_ranks);
		return [flush, $flush_ranks[0]/*Wert der höchsten Karte im Flush*/, $flush_ranks[1]/*...*/, $flush_ranks[2], $flush_ranks[3], $flush_ranks[4]];
	}

	$straight_count = ($rank_count['14'] ? 1 : 0);
	$straight_rank = 0;
	for ($r = 2; $r <= 14; $r += 1) {
		if ($rank_count[$r]) {
			$straight_count += 1;
			if ($straight_count >= 5) {
				$straight_rank = $r;
			}
		} else {
			$straight_count = 0;
		}
	}
	if ($straight_rank)
		return [straight, $straight_rank/*Wert der höchsten Karte in der Straße*/];

	if ($max_rank_count[1] == 3 && $max_rank_count[2] >= 2) // Wert der am öftesten vorkommt kommt 3 Mal vor UND Wert der am zweitöftesten vorkommt kommt 2 Mal vor:
		return [full_house, $max_rank[1]/*Wert des Drillings*/, $max_rank[2]/*Wert des Paares*/];

	if ($max_rank_count[1] == 3) // Wert der am öftesten vorkommt kommt 3 Mal vor:
		return [three_of_a_kind, $max_rank[1]/*Wert des Drillings*/, $max_rank[2], $max_rank[3]];

	
	if ($max_rank_count[1] == 2) { // Wert der am öftesten vorkommt kommt 2 Mal vor:
		if ($max_rank_count[2] == 2)
			return [two_pair, $max_rank[1]/*Wert des 1. Paares*/, $max_rank[2]/*Wert des 2. Paares*/, $max_rank[3]];
		return [one_pair, $max_rank[1]/*Wert des Paares*/, $max_rank[2], $max_rank[3], $max_rank[4]];
	}

	// Spierler hat nichts => höchste bis fünfthöchste Karte werden zurückgegeben:
	return [high_card, $max_rank[1], $max_rank[2], $max_rank[3], $max_rank[4], $max_rank[5]];
}
