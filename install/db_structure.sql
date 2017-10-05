SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


CREATE TABLE `rplib_attribute` (
  `id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `reference` int(11) NOT NULL,
  `value` blob
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `rplib_attribute_reference` (
  `id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `type` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `rplib_game` (
  `id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `rplib_game_attributes` (
  `game` int(11) NOT NULL,
  `attribute` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `rplib_game_players` (
  `game` int(11) NOT NULL,
  `player` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `rplib_game_statistics` (
  `game` int(11) NOT NULL,
  `statistic` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `rplib_game_turns` (
  `game` int(11) NOT NULL,
  `turn` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `rplib_player` (
  `id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `rplib_player_attributes` (
  `player` int(11) NOT NULL,
  `attribute` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `rplib_player_statistics` (
  `player` int(11) NOT NULL,
  `statistic` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `rplib_statistic` (
  `id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `reference` int(11) NOT NULL,
  `value` blob
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `rplib_statistic_reference` (
  `id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `type` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `rplib_turn` (
  `id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `player` int(11) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `rplib_turn_attributes` (
  `turn` int(11) NOT NULL,
  `attribute` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `rplib_turn_statistics` (
  `turn` int(11) NOT NULL,
  `statistic` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE `rplib_attribute`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reference` (`reference`);

ALTER TABLE `rplib_attribute_reference`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `rplib_game`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `rplib_game_attributes`
  ADD PRIMARY KEY (`game`,`attribute`),
  ADD KEY `attribute` (`attribute`);

ALTER TABLE `rplib_game_players`
  ADD PRIMARY KEY (`game`,`player`),
  ADD KEY `player` (`player`);

ALTER TABLE `rplib_game_statistics`
  ADD PRIMARY KEY (`game`,`statistic`),
  ADD KEY `statistic` (`statistic`);

ALTER TABLE `rplib_game_turns`
  ADD PRIMARY KEY (`game`,`turn`),
  ADD KEY `turn` (`turn`);

ALTER TABLE `rplib_player`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `rplib_player_attributes`
  ADD PRIMARY KEY (`player`,`attribute`),
  ADD KEY `attribute` (`attribute`);

ALTER TABLE `rplib_player_statistics`
  ADD PRIMARY KEY (`player`,`statistic`),
  ADD KEY `statistic` (`statistic`);

ALTER TABLE `rplib_statistic`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reference` (`reference`);

ALTER TABLE `rplib_statistic_reference`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `rplib_turn`
  ADD PRIMARY KEY (`id`),
  ADD KEY `player` (`player`);

ALTER TABLE `rplib_turn_attributes`
  ADD PRIMARY KEY (`turn`,`attribute`),
  ADD KEY `attribute` (`attribute`);

ALTER TABLE `rplib_turn_statistics`
  ADD PRIMARY KEY (`turn`,`statistic`),
  ADD KEY `statistic` (`statistic`);


ALTER TABLE `rplib_attribute`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `rplib_attribute_reference`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `rplib_game`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `rplib_player`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `rplib_statistic`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `rplib_statistic_reference`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `rplib_turn`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `rplib_attribute`
  ADD CONSTRAINT `rplib_attribute_ibfk_1` FOREIGN KEY (`reference`) REFERENCES `rplib_attribute_reference` (`id`);

ALTER TABLE `rplib_game_attributes`
  ADD CONSTRAINT `rplib_game_attributes_ibfk_1` FOREIGN KEY (`attribute`) REFERENCES `rplib_attribute` (`id`),
  ADD CONSTRAINT `rplib_game_attributes_ibfk_2` FOREIGN KEY (`game`) REFERENCES `rplib_game` (`id`);

ALTER TABLE `rplib_game_players`
  ADD CONSTRAINT `rplib_game_players_ibfk_1` FOREIGN KEY (`game`) REFERENCES `rplib_game` (`id`),
  ADD CONSTRAINT `rplib_game_players_ibfk_2` FOREIGN KEY (`player`) REFERENCES `rplib_player` (`id`);

ALTER TABLE `rplib_game_statistics`
  ADD CONSTRAINT `rplib_game_statistics_ibfk_1` FOREIGN KEY (`game`) REFERENCES `rplib_game` (`id`),
  ADD CONSTRAINT `rplib_game_statistics_ibfk_2` FOREIGN KEY (`statistic`) REFERENCES `rplib_statistic` (`id`);

ALTER TABLE `rplib_game_turns`
  ADD CONSTRAINT `rplib_game_turns_ibfk_1` FOREIGN KEY (`game`) REFERENCES `rplib_game` (`id`),
  ADD CONSTRAINT `rplib_game_turns_ibfk_2` FOREIGN KEY (`turn`) REFERENCES `rplib_turn` (`id`);

ALTER TABLE `rplib_player_attributes`
  ADD CONSTRAINT `rplib_player_attributes_ibfk_1` FOREIGN KEY (`attribute`) REFERENCES `rplib_attribute` (`id`),
  ADD CONSTRAINT `rplib_player_attributes_ibfk_2` FOREIGN KEY (`player`) REFERENCES `rplib_player` (`id`);

ALTER TABLE `rplib_player_statistics`
  ADD CONSTRAINT `rplib_player_statistics_ibfk_1` FOREIGN KEY (`player`) REFERENCES `rplib_player` (`id`),
  ADD CONSTRAINT `rplib_player_statistics_ibfk_2` FOREIGN KEY (`statistic`) REFERENCES `rplib_statistic` (`id`);

ALTER TABLE `rplib_statistic`
  ADD CONSTRAINT `rplib_statistic_ibfk_1` FOREIGN KEY (`reference`) REFERENCES `rplib_statistic_reference` (`id`);

ALTER TABLE `rplib_turn`
  ADD CONSTRAINT `rplib_turn_ibfk_1` FOREIGN KEY (`player`) REFERENCES `rplib_player` (`id`);

ALTER TABLE `rplib_turn_attributes`
  ADD CONSTRAINT `rplib_turn_attributes_ibfk_1` FOREIGN KEY (`attribute`) REFERENCES `rplib_attribute` (`id`),
  ADD CONSTRAINT `rplib_turn_attributes_ibfk_2` FOREIGN KEY (`turn`) REFERENCES `rplib_turn` (`id`);

ALTER TABLE `rplib_turn_statistics`
  ADD CONSTRAINT `rplib_turn_statistics_ibfk_1` FOREIGN KEY (`statistic`) REFERENCES `rplib_statistic` (`id`),
  ADD CONSTRAINT `rplib_turn_statistics_ibfk_2` FOREIGN KEY (`turn`) REFERENCES `rplib_turn` (`id`);
