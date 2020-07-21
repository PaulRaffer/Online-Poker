<?php

define("debug", false);

// PHASE:
define("before_start", 0);
define("preflop", 1);
define("flop", 2);
define("turn", 3);
define("river", 4);
define("showdown", 5);
define("dealing", 6);

define("phase_num_to_str", [
	before_start => "Before start",
	preflop => "Preflop",
	flop => "Flop",
	turn => "Turn",
	river => "River",
	showdown => "Showdown",
	dealing => "Showdown",
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
