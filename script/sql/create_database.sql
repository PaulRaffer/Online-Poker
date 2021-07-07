DROP   DATABASE IF EXISTS `poker`;
CREATE DATABASE           `poker`;
USE                       `poker`;



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
	`id`    INT     NOT NULL AUTO_INCREMENT UNIQUE,
	`front` CHAR(8) NOT NULL,
	`back`  CHAR(8) NOT NULL,
	`rank`  CHAR    NOT NULL,
	`suit`  CHAR    NOT NULL,
	
	PRIMARY KEY (`id`  ),
	FOREIGN KEY (`rank`) REFERENCES `ranks` (`symbol`),
	FOREIGN KEY (`suit`) REFERENCES `suits` (`symbol`)
);

INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0A1', '&#x1F0A0', 'A', '♠');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0A2', '&#x1F0A0', '2', '♠');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0A3', '&#x1F0A0', '3', '♠');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0A4', '&#x1F0A0', '4', '♠');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0A5', '&#x1F0A0', '5', '♠');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0A6', '&#x1F0A0', '6', '♠');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0A7', '&#x1F0A0', '7', '♠');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0A8', '&#x1F0A0', '8', '♠');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0A9', '&#x1F0A0', '9', '♠');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0AA', '&#x1F0A0', 'T', '♠');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0AB', '&#x1F0A0', 'J', '♠');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0AD', '&#x1F0A0', 'Q', '♠');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0AE', '&#x1F0A0', 'K', '♠');

INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0B1', '&#x1F0A0', 'A', '♥');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0B2', '&#x1F0A0', '2', '♥');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0B3', '&#x1F0A0', '3', '♥');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0B4', '&#x1F0A0', '4', '♥');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0B5', '&#x1F0A0', '5', '♥');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0B6', '&#x1F0A0', '6', '♥');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0B7', '&#x1F0A0', '7', '♥');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0B8', '&#x1F0A0', '8', '♥');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0B9', '&#x1F0A0', '9', '♥');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0BA', '&#x1F0A0', 'T', '♥');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0BB', '&#x1F0A0', 'J', '♥');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0BD', '&#x1F0A0', 'Q', '♥');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0BE', '&#x1F0A0', 'K', '♥');

INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0C1', '&#x1F0A0', 'A', '♦');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0C2', '&#x1F0A0', '2', '♦');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0C3', '&#x1F0A0', '3', '♦');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0C4', '&#x1F0A0', '4', '♦');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0C5', '&#x1F0A0', '5', '♦');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0C6', '&#x1F0A0', '6', '♦');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0C7', '&#x1F0A0', '7', '♦');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0C8', '&#x1F0A0', '8', '♦');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0C9', '&#x1F0A0', '9', '♦');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0CA', '&#x1F0A0', 'T', '♦');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0CB', '&#x1F0A0', 'J', '♦');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0CD', '&#x1F0A0', 'Q', '♦');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0CE', '&#x1F0A0', 'K', '♦');


INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0D1', '&#x1F0A0', 'A', '♣');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0D2', '&#x1F0A0', '2', '♣');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0D3', '&#x1F0A0', '3', '♣');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0D4', '&#x1F0A0', '4', '♣');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0D5', '&#x1F0A0', '5', '♣');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0D6', '&#x1F0A0', '6', '♣');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0D7', '&#x1F0A0', '7', '♣');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0D8', '&#x1F0A0', '8', '♣');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0D9', '&#x1F0A0', '9', '♣');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0DA', '&#x1F0A0', 'T', '♣');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0DB', '&#x1F0A0', 'J', '♣');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0DD', '&#x1F0A0', 'Q', '♣');
INSERT INTO `cards` (`front`, `back`, `rank`, `suit`) VALUES ('&#x1F0DE', '&#x1F0A0', 'K', '♣');


CREATE TABLE `actions` (
	`id`   INT NOT NULL UNIQUE AUTO_INCREMENT,
	`name` VARCHAR(32),

	PRIMARY KEY (`id`)
);

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
	`id`                 INT         UNIQUE NOT NULL AUTO_INCREMENT,
	`name`               VARCHAR(32) UNIQUE NOT NULL,
	`small_blind_money`  INT                DEFAULT 25,
	`big_blind_money`    INT                DEFAULT 50,
	`phase`              INT                DEFAULT NULL,
	`dealer`             INT         UNIQUE DEFAULT NULL,
	`current_player`     INT         UNIQUE DEFAULT NULL,
	`card1`              INT                DEFAULT NULL,
	`card2`              INT                DEFAULT NULL,
	`card3`              INT                DEFAULT NULL,
	`card4`              INT                DEFAULT NULL,
	`card5`              INT                DEFAULT NULL,
	`pot_money`          INT                DEFAULT NULL,
	`highest_bet`        INT                DEFAULT NULL,
	`highest_bet_player` INT         UNIQUE DEFAULT NULL,
	`highest_raise`      INT                DEFAULT NULL,
	
	PRIMARY KEY (`id`)
);

CREATE TABLE `players` (
	`id`          INT     NOT NULL UNIQUE AUTO_INCREMENT,
	`user`        INT,
	`game`        INT     NOT NULL,
	`next_player` INT     DEFAULT NULL,
	`money`       INT     DEFAULT 10000,
	`card1`       INT     DEFAULT NULL,
	`card2`       INT     DEFAULT NULL,
	`is_winner`   BOOLEAN DEFAULT FALSE,
	`last_action` INT     DEFAULT NULL,
	`bet`         INT,
	
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
ALTER TABLE `games` ADD CONSTRAINT FOREIGN KEY (`highest_bet_player`) REFERENCES `players` (`id`);
