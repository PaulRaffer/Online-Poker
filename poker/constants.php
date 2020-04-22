<?php

define("debug", false);

// PHASE:
define("phase0", 0);
define("dealing", 1);
define("preflop", 2);
define("flop", 3);
define("turn", 4);
define("river", 5);
define("showdown", 6);

define("phase_num_to_str", [
	phase0 => "Phase0",
	dealing => "Dealing",
	preflop => "Preflop",
	flop => "Flop",
	turn => "Turn",
	river => "River",
	showdown => "Showdown",
	showdown+1 => "Showdown",
]);


// ACTION:
define("call", 1);
define("raise", 2);
define("fold", 3);
        
define("action_num_to_str", [
    call => "Check/Call",
    raise => "Raise",
    fold => "Fold",
]);
