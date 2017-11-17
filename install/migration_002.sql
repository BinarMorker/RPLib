SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

ALTER TABLE `rplib_attribute`
  CHANGE `value` `version` INT(11) NOT NULL;

CREATE TABLE `rplib_attribute_version` (
  `attribute` int(11) NOT NULL,
  `version` int(11) NOT NULL,
  `value` blob NOT NULL,
  PRIMARY KEY (`attribute`,`version`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `rplib_attribute_version`
  ADD INDEX `rplib_attribute_version_indx_1` (`version`);

ALTER TABLE `rplib_attribute`
  ADD CONSTRAINT `rplib_attribute_ibfk_2` FOREIGN KEY (`version`) REFERENCES `rplib_attribute_version` (`version`);

ALTER TABLE `rplib_attribute_version`
  ADD CONSTRAINT `rplib_attribute_version_ibfk_1` FOREIGN KEY (`attribute`) REFERENCES `rplib_attribute` (`id`);

ALTER TABLE `rplib_game_players`
  ADD COLUMN `position` int(11) NOT NULL;

ALTER TABLE `rplib_game_players`
  ADD UNIQUE  (`game`, `position`);