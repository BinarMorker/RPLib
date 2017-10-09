<?php

namespace RPLib\Entities;

use OutOfBoundsException;
use OutOfRangeException;
use RPLib\Core\GameManager;
use RPLib\Entities\Interfaces\IEntity;
use RPLib\Entities\Relations\LinkedEntity;
use RPLib\Entities\Traits\Entity;
use RPLib\Enums\TurnStatus;
use UnexpectedValueException;

/**
 * Class Game
 * @package RPLib\Entities
 */
class Game implements IEntity {
    use Entity {
        Entity::save as private saveParameters;
        Entity::setAttribute as private __setAttribute;
        Entity::setStatistic as private __setStatistic;
    }

    /**
     * @var Turn[]
     */
    private $turns;

    /**
     * @var Player[]
     */
    private $players;

    /**
     * @var LinkedEntity
     */
    private $playerLink;

    /**
     * @var LinkedEntity
     */
    private $turnLink;

    /**
     * Game constructor.
     * @param int|null $id
     */
    public function __construct(int $id = null) {
        $this->initialize('rplib_game', $id);

        $this->attributeLink = new LinkedEntity('rplib_game_attributes', 'game', 'attribute', Attribute::class);
        $this->statisticLink = new LinkedEntity('rplib_game_statistics', 'game', 'statistic', Statistic::class);
        $this->playerLink = new LinkedEntity('rplib_game_players', 'game', 'player', Player::class);
        $this->turnLink = new LinkedEntity('rplib_turn', 'game', 'id', Turn::class);

        if (!is_null($this->id)) {
            $this->players = $this->loadLinked($this->playerLink);
            $this->turns = $this->loadLinked($this->turnLink);
            $this->attributes = $this->loadLinked($this->attributeLink);
            $this->statistics = $this->loadLinked($this->statisticLink);
        } else {
            $this->players = [];
            $this->turns = [];
            $this->attributes = [];
            $this->statistics = [];
        }
    }

    /**
     * @return Turn[]
     */
    public function getTurns() : array {
        return $this->turns;
    }

    /**
     * @return Turn
     */
    public function getCurrentTurn() : Turn {
        $lastTurn = end($this->turns);

        switch ($lastTurn->getStatus()) {
            case TurnStatus::PAUSED:
            case TurnStatus::RETURNED_THEN_PAUSED:
                $index = key($lastTurn);

                if ($index > 0) {
                    return $this->turns[$index - 1];
                } else {
                    return $lastTurn;
                }

                break;
            case TurnStatus::NOT_STARTED:
            case TurnStatus::ONGOING:
            case TurnStatus::FINISHED:
            case TurnStatus::SKIPPED:
            case TurnStatus::FINISHED_THEN_RETURNED:
            case TurnStatus::SKIPPED_THEN_RETURNED:
            case TurnStatus::RETURNED_THEN_FINISHED:
            case TurnStatus::RETURNED_THEN_SKIPPED:
            case TurnStatus::PAUSED_THEN_RETURNED:
            default:
                return $lastTurn;
                break;
        }
    }

    public function addTurn(Turn $turn) {
        $this->turns[] = $turn;
    }

    /**
     * @return Turn
     */
    public function getNextTurn() : Turn {
        // TODO: Evaluate the need for this method instead of just using getNextPlayer()
        $player = $this->getNextPlayer();
        $turn = new Turn();
        $turn->setPlayer($player);
        return $turn;
    }

    /**
     * @return Player[]
     */
    public function getPlayers() : array {
        return $this->players;
    }

    /**
     * @return Player
     */
    public function getNextPlayer() : Player {
        if (count($this->players) == 0) {
            throw new OutOfBoundsException("There are no player to select");
        }

        if (count($this->turns) > 0) {
            $lastTurn = end($this->turns);
            $lastPlayer = $lastTurn->getPlayer();
            $filter = array_filter($this->players, function (Player $item) use ($lastPlayer) {
                return $item->getName() == $lastPlayer->getName();
            });
            $nextIndex = (key($filter) + 1) % count($this->players);
            return $this->players[$nextIndex];
        } else {
            return $this->players[0];
        }
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function hasPlayer(Player $player) {
        if (count($this->players) == 0) {
            return false;
        }

        $filter = array_filter($this->players, function(Player $item) use ($player) {
            return $item->getName() == $player->getName();
        });

        return count($filter) > 0;
    }

    /**
     * @param Player $player
     */
    public function addPlayer(Player $player) {
        if (!$this->hasPlayer($player)) {
            $this->players[] = $player;
        }
    }

    /**
     * @param Attribute $attribute
     */
    public function setAttribute(Attribute $attribute) {
        /*if ($this->hasAttribute($attribute->getName())) {
            throw new UnexpectedValueException("The attribute \"{$attribute->getName()}\" is already present.");
        }*/

        if (!GameManager::getInstance()->getRegistry()->games->hasAttribute($attribute->getReference())) {
            throw new OutOfRangeException("The attribute \"{$attribute->getName()}\" can't be put on a game.");
        }

        $this->__setAttribute($attribute);
    }

    /**
     * @param Statistic $statistic
     */
    public function setStatistic(Statistic $statistic) {
        /*if ($this->hasStatistic($statistic->getName())) {
            throw new UnexpectedValueException("The statistic \"{$statistic->getName()}\" is already present.");
        }*/

        if (!GameManager::getInstance()->getRegistry()->games->hasStatistic($statistic->getReference())) {
            throw new OutOfRangeException("The statistic \"{$statistic->getName()}\" can't be put on a game.");
        }

        $this->__setStatistic($statistic);
    }

    /**
     *
     */
    public function save() {
        $this->saveParameters([
            'name' => $this->getName()
        ]);

        foreach ($this->players as $player) {
            $player->save();
            $this->saveLinked($this->playerLink, $player);
        }

        foreach ($this->turns as $turn) {
            $turn->save();
            $this->saveLinked($this->turnLink, $turn);
        }
    }
}