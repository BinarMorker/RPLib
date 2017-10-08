SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

ALTER TABLE `rplib_turn`
  ADD COLUMN `game` int(11) NULL;

UPDATE `rplib_turn` SET `rplib_turn`.`game` = (
  SELECT `rplib_game_turns`.`game`
  FROM `rplib_game_turns`
  WHERE `rplib_game_turns`.`turn` = `rplib_turn`.`id`
);

DROP TABLE `rplib_game_turns`;

ALTER TABLE `rplib_turn`
  MODIFY COLUMN `game` int(11) NOT NULL;

ALTER TABLE `rplib_turn`
  ADD KEY `game` (`game`);

ALTER TABLE `rplib_turn`
  ADD CONSTRAINT `rplib_turn_game_fk` FOREIGN KEY (`game`) REFERENCES `rplib_game` (`id`);