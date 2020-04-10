CREATE DATABASE `poker`;
USE             `poker`;



CREATE TABLE `ranks` (
	`symbol` CHAR       NOT NULL UNIQUE,
	`name`   VARCHAR(8) NOT NULL UNIQUE,
	`value`  INT                 UNIQUE,
	
	PRIMARY KEY (`symbol`)
);

INSERT INTO `ranks` (`symbol`, `name`, `value`) VALUES ('A', 'ace'  , NULL);
INSERT INTO `ranks` (`symbol`, `name`, `value`) VALUES ('2', 'two'  ,  2  );
INSERT INTO `ranks` (`symbol`, `name`, `value`) VALUES ('3', 'three',  3  );
INSERT INTO `ranks` (`symbol`, `name`, `value`) VALUES ('4', 'four' ,  4  );
INSERT INTO `ranks` (`symbol`, `name`, `value`) VALUES ('5', 'five' ,  5  );
INSERT INTO `ranks` (`symbol`, `name`, `value`) VALUES ('6', 'six'  ,  6  );
INSERT INTO `ranks` (`symbol`, `name`, `value`) VALUES ('7', 'seven',  7  );
INSERT INTO `ranks` (`symbol`, `name`, `value`) VALUES ('8', 'eight',  8  );
INSERT INTO `ranks` (`symbol`, `name`, `value`) VALUES ('9', 'nine' ,  9  );
INSERT INTO `ranks` (`symbol`, `name`, `value`) VALUES ('T', 'ten'  , 10  );
INSERT INTO `ranks` (`symbol`, `name`, `value`) VALUES ('J', 'jack' , 11  );
INSERT INTO `ranks` (`symbol`, `name`, `value`) VALUES ('Q', 'queen', 12  );
INSERT INTO `ranks` (`symbol`, `name`, `value`) VALUES ('K', 'king' , 13  );



CREATE TABLE `suits`(
	`symbol` CHAR       NOT NULL UNIQUE,
	`name`   VARCHAR(8) NOT NULL UNIQUE,
	
	PRIMARY KEY (`symbol`)
);

INSERT INTO `suits` (`symbol`, `name`) VALUES ('♠', 'spades'  );
INSERT INTO `suits` (`symbol`, `name`) VALUES ('♥', 'hearts'  );
INSERT INTO `suits` (`symbol`, `name`) VALUES ('♦', 'diamonds');
INSERT INTO `suits` (`symbol`, `name`) VALUES ('♣', 'clubs'   );



CREATE TABLE `cards` (
	`id`      INT  NOT NULL AUTO_INCREMENT UNIQUE,
	`symbol`  CHAR NOT NULL,
	`rank`    CHAR NOT NULL,
	`suit`    CHAR NOT NULL,
	
	PRIMARY KEY (`id`  ),
	FOREIGN KEY (`rank`) REFERENCES `ranks` (`symbol`),
	FOREIGN KEY (`suit`) REFERENCES `suits` (`symbol`)
);

INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🂡', 'A', '♠');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🂢', '2', '♠');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🂣', '3', '♠');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🂤', '4', '♠');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🂥', '5', '♠');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🂦', '6', '♠');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🂧', '7', '♠');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🂨', '8', '♠');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🂩', '9', '♠');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🂪', 'T', '♠');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🂫', 'J', '♠');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🂭', 'Q', '♠');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🂮', 'K', '♠');

INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🂱', 'A', '♥');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🂲', '2', '♥');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🂳', '3', '♥');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🂴', '4', '♥');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🂵', '5', '♥');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🂶', '6', '♥');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🂷', '7', '♥');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🂸', '8', '♥');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🂹', '9', '♥');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🂺', 'T', '♥');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🂻', 'J', '♥');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🂽', 'Q', '♥');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🂾', 'K', '♥');

INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🃁', 'A', '♦');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🃂', '2', '♦');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🃃', '3', '♦');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🃄', '4', '♦');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🃅', '5', '♦');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🃆', '6', '♦');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🃇', '7', '♦');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🃈', '8', '♦');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🃉', '9', '♦');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🃊', 'T', '♦');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🃋', 'J', '♦');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🃍', 'Q', '♦');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🃎', 'K', '♦');


INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🃑', 'A', '♣');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🃒', '2', '♣');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🃓', '3', '♣');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🃔', '4', '♣');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🃕', '5', '♣');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🃖', '6', '♣');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🃗', '7', '♣');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🃘', '8', '♣');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🃙', '9', '♣');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🃚', 'T', '♣');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🃛', 'J', '♣');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🃝', 'Q', '♣');
INSERT INTO `cards` (`symbol`, `rank`, `suit`) VALUES ('🃞', 'K', '♣');


CREATE TABLE `actions` (
	`id`   INT NOT NULL UNIQUE AUTO_INCREMENT,
	`name` VARCHAR(32),

	PRIMARY KEY (`id`)
);

INSERT INTO `actions` (`name`) VALUES ('check');
INSERT INTO `actions` (`name`) VALUES ('call');
INSERT INTO `actions` (`name`) VALUES ('raise');
INSERT INTO `actions` (`name`) VALUES ('fold');


CREATE TABLE `users` (
	`id`         INT          NOT NULL UNIQUE AUTO_INCREMENT,
	`username`   VARCHAR( 32) NOT NULL UNIQUE,
	`password`   VARCHAR(255) NOT NULL,
	`firstname`  VARCHAR( 16) DEFAULT NULL,
	`lastname`   VARCHAR( 16) DEFAULT NULL,
	`created_at` DATETIME     DEFAULT CURRENT_TIMESTAMP,
	
	PRIMARY KEY (`id`)
);

CREATE TABLE `games` (
	`id`             INT         UNIQUE NOT NULL AUTO_INCREMENT,
	`name`           VARCHAR(32) UNIQUE NOT NULL,
	`phase`          INT                DEFAULT NULL,
	`dealer`         INT         UNIQUE DEFAULT NULL,
	`current_player` INT         UNIQUE DEFAULT NULL,
	`card1`          INT                DEFAULT NULL,
	`card2`          INT                DEFAULT NULL,
	`card3`          INT                DEFAULT NULL,
	`card4`          INT                DEFAULT NULL,
	`card5`          INT                DEFAULT NULL,
	`pot_money`      INT                DEFAULT NULL,
	
	PRIMARY KEY (`id`)
);

CREATE TABLE `players` (
	`id`          INT NOT NULL UNIQUE AUTO_INCREMENT,
	`user`        INT,
	`game`        INT NOT NULL,
	`next_player` INT DEFAULT NULL,
	`money`       INT,
	`card1`       INT DEFAULT NULL UNIQUE,
	`card2`       INT DEFAULT NULL UNIQUE,
	`last_action` INT,
	
	PRIMARY KEY (`id`         ),
	FOREIGN KEY (`user`       ) REFERENCES `users`   (`id`),
	FOREIGN KEY (`game`       ) REFERENCES `games`   (`id`),
	FOREIGN KEY (`next_player`) REFERENCES `players` (`id`),
	FOREIGN KEY (`card1`      ) REFERENCES `cards`   (`id`),
	FOREIGN KEY (`card2`      ) REFERENCES `cards`   (`id`),
	FOREIGN KEY (`last_action`) REFERENCES `actions` (`id`)
);

ALTER TABLE `games` ADD CONSTRAINT FOREIGN KEY (`dealer`) REFERENCES `players` (`id`);
ALTER TABLE `games` ADD CONSTRAINT FOREIGN KEY (`current_player`) REFERENCES `players` (`id`);

