<?php

namespace RPLib\Entities;

use OutOfRangeException;
use RPLib\Core\GameManager;
use RPLib\Entities\Interfaces\IEntity;
use RPLib\Entities\Relations\LinkedEntity;
use RPLib\Entities\Relations\StorageField;
use RPLib\Entities\Traits\Entity;
use RPLib\Enums\TurnStatus;

/**
 * Class Turn
 * @package RPLib\Entities
 */
class Turn implements IEntity {
    use Entity {
        Entity::save as private saveParameters;
        Entity::setAttribute as private __setAttribute;
        Entity::setStatistic as private __setStatistic;
    }

    /**
     * @var Player
     */
    private $player;

    /**
     * @var int
     */
    private $status;
    
    /**
     * @var Game
     */
    private $game;

    /**
     * Turn constructor.
     * @param int|null $id
     */
    public function __construct(int $id = null) {
        $this->initialize('rplib_turn', $id);

        $this->attributeLink = new LinkedEntity('rplib_turn_attributes', 'turn', 'attribute', Attribute::class);
        $this->statisticLink = new LinkedEntity('rplib_turn_statistics', 'turn', 'statistic', Statistic::class);

        if (!is_null($this->id)) {
            $this->load([
                new StorageField('player', function($value) {
                    $this->player = new Player($value);
                }),
                new StorageField('status', function($value) {
                    $this->status = $value;
                }),
                new StorageField('game', function($value) {
                    $this->game = $value;
                })
            ]);

            $this->attributes = $this->loadLinked($this->attributeLink);
            $this->statistics = $this->loadLinked($this->statisticLink);
        } else {
            $this->status = TurnStatus::NOT_STARTED;
            $this->attributes = [];
            $this->statistics = [];
        }
    }

    /**
     * @param Attribute $attribute
     */
    public function setAttribute(Attribute $attribute) {
        if (!GameManager::getInstance()->getRegistry()->turns->hasAttribute($attribute->getReference())) {
            throw new OutOfRangeException("The attribute \"{$attribute->getName()}\" can't be put on a turn.");
        }

        $this->__setAttribute($attribute);
    }

    /**
     * @param Statistic $statistic
     */
    public function setStatistic(Statistic $statistic) {
        if (!GameManager::getInstance()->getRegistry()->turns->hasStatistic($statistic->getReference())) {
            throw new OutOfRangeException("The statistic \"{$statistic->getName()}\" can't be put on a turn.");
        }

        $this->__setStatistic($statistic);
    }

    /**
     * @return Player|null
     */
    public function getPlayer() : Player {
        return $this->player;
    }

    /**
     * @param Player $player
     */
    public function setPlayer(Player $player) {
        $this->player = $player;
    }
    
    /**
     * @return Game|null
     */
    public function getGame() : Game {
        return new Game($this->game);
    }
    
    /**
     * @param Game $game
     */
    public function setGame(Game $game) {
        $this->game = $game->getId();
    }

    /**
     * @return int
     */
    public function getStatus() : int {
        return $this->status;
    }

    /**
     * @return bool
     */
    public function isReturnable() : bool {
        switch ($this->status) {
            case TurnStatus::RETURNED_THEN_FINISHED:
            case TurnStatus::RETURNED_THEN_SKIPPED:
            case TurnStatus::RETURNED_THEN_PAUSED:
            case TurnStatus::NOT_STARTED:
            case TurnStatus::FINISHED:
            case TurnStatus::SKIPPED:
                return $this->getGame()->getCurrentTurn()->getId() == $this->getId();
                break;
            case TurnStatus::ONGOING:
            case TurnStatus::PAUSED:
            case TurnStatus::FINISHED_THEN_RETURNED:
            case TurnStatus::SKIPPED_THEN_RETURNED:
            case TurnStatus::PAUSED_THEN_RETURNED:
            default:
                return true;
                break;
        }
    }

    /**
     * @return bool
     */
    public function isFinished() : bool {
        switch ($this->status) {
            case TurnStatus::RETURNED_THEN_FINISHED:
            case TurnStatus::FINISHED:
                return true;
                break;
            case TurnStatus::RETURNED_THEN_SKIPPED:
            case TurnStatus::RETURNED_THEN_PAUSED:
            case TurnStatus::NOT_STARTED:
            case TurnStatus::SKIPPED:
            case TurnStatus::ONGOING:
            case TurnStatus::PAUSED:
            case TurnStatus::FINISHED_THEN_RETURNED:
            case TurnStatus::SKIPPED_THEN_RETURNED:
            case TurnStatus::PAUSED_THEN_RETURNED:
            default:
                return false;
                break;
        }
    }

    /**
     * @return bool
     */
    public function isSkipped() : bool {
        switch ($this->status) {
            case TurnStatus::RETURNED_THEN_SKIPPED:
            case TurnStatus::SKIPPED:
                return true;
                break;
            case TurnStatus::RETURNED_THEN_FINISHED:
            case TurnStatus::FINISHED:
            case TurnStatus::RETURNED_THEN_PAUSED:
            case TurnStatus::NOT_STARTED:
            case TurnStatus::ONGOING:
            case TurnStatus::PAUSED:
            case TurnStatus::FINISHED_THEN_RETURNED:
            case TurnStatus::SKIPPED_THEN_RETURNED:
            case TurnStatus::PAUSED_THEN_RETURNED:
            default:
                return false;
                break;
        }
    }

    /**
     * @return bool
     */
    public function isOngoing() : bool {
        switch ($this->status) {
            case TurnStatus::ONGOING:
            case TurnStatus::PAUSED:
            case TurnStatus::FINISHED_THEN_RETURNED:
            case TurnStatus::SKIPPED_THEN_RETURNED:
            case TurnStatus::PAUSED_THEN_RETURNED:
            case TurnStatus::RETURNED_THEN_PAUSED:
                return true;
                break;
            case TurnStatus::RETURNED_THEN_SKIPPED:
            case TurnStatus::SKIPPED:
            case TurnStatus::RETURNED_THEN_FINISHED:
            case TurnStatus::FINISHED:
            case TurnStatus::NOT_STARTED:
            default:
                return false;
                break;
        }
    }

    /**
     *
     */
    public function finish() {
        switch ($this->status) {
            case TurnStatus::FINISHED_THEN_RETURNED:
            case TurnStatus::SKIPPED_THEN_RETURNED:
            case TurnStatus::RETURNED_THEN_FINISHED:
            case TurnStatus::RETURNED_THEN_SKIPPED:
            case TurnStatus::RETURNED_THEN_PAUSED:
            case TurnStatus::PAUSED_THEN_RETURNED:
                $this->status = TurnStatus::RETURNED_THEN_FINISHED;
                break;
            case TurnStatus::NOT_STARTED:
                // TODO: Evaluate the need to "skip" the turn here instead of insisting on calling skip() from the implementation instead
            case TurnStatus::FINISHED:
            case TurnStatus::SKIPPED:
            case TurnStatus::ONGOING:
            case TurnStatus::PAUSED:
            default:
                $this->status = TurnStatus::FINISHED;
                break;
        }
    }

    /**
     *
     */
    public function skip() {
        switch ($this->status) {
            case TurnStatus::FINISHED_THEN_RETURNED:
            case TurnStatus::SKIPPED_THEN_RETURNED:
            case TurnStatus::RETURNED_THEN_FINISHED:
            case TurnStatus::RETURNED_THEN_SKIPPED:
            case TurnStatus::RETURNED_THEN_PAUSED:
            case TurnStatus::PAUSED_THEN_RETURNED:
                $this->status = TurnStatus::RETURNED_THEN_SKIPPED;
                break;
            case TurnStatus::NOT_STARTED:
            case TurnStatus::FINISHED:
            case TurnStatus::SKIPPED:
            case TurnStatus::ONGOING:
            case TurnStatus::PAUSED:
            default:
                $this->status = TurnStatus::SKIPPED;
                break;
        }
    }

    /**
     *
     */
    public function begin() {
        switch ($this->status) {
            case TurnStatus::RETURNED_THEN_FINISHED:
            case TurnStatus::FINISHED_THEN_RETURNED:
            case TurnStatus::FINISHED:
                $this->status = TurnStatus::FINISHED_THEN_RETURNED;
                break;
            case TurnStatus::RETURNED_THEN_SKIPPED:
            case TurnStatus::SKIPPED_THEN_RETURNED:
            case TurnStatus::SKIPPED:
                $this->status = TurnStatus::SKIPPED_THEN_RETURNED;
                break;
            case TurnStatus::RETURNED_THEN_PAUSED:
            case TurnStatus::PAUSED_THEN_RETURNED:
                $this->status = TurnStatus::PAUSED_THEN_RETURNED;
                break;
            case TurnStatus::NOT_STARTED:
            case TurnStatus::ONGOING:
            case TurnStatus::PAUSED:
            default:
                $this->status = TurnStatus::ONGOING;
                break;
        }
    }

    /**
     *
     */
    public function pause() {
        switch ($this->status) {
            case TurnStatus::RETURNED_THEN_PAUSED:
            case TurnStatus::RETURNED_THEN_FINISHED:
            case TurnStatus::RETURNED_THEN_SKIPPED:
            case TurnStatus::FINISHED_THEN_RETURNED:
            case TurnStatus::SKIPPED_THEN_RETURNED:
            case TurnStatus::PAUSED_THEN_RETURNED:
                $this->status = TurnStatus::RETURNED_THEN_PAUSED;
                break;
            case TurnStatus::FINISHED:
            case TurnStatus::SKIPPED:
            case TurnStatus::NOT_STARTED:
            case TurnStatus::ONGOING:
            case TurnStatus::PAUSED:
            default:
                $this->status = TurnStatus::PAUSED;
                break;
        }
    }

    public function save() {
        $this->saveParameters([
            'name' => $this->getName(),
            'player' => (!is_null($this->player)) ? $this->player->getId() : null,
            'game' => (!is_null($this->game)) ? $this->game : null,
            'status' => $this->status
        ]);
    }
}