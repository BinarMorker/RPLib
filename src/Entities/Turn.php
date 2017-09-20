<?php

namespace RPLib\Entities;

use OutOfRangeException;
use RPLib\Core\GameManager;
use RPLib\Entities\Relations\LinkedEntity;
use RPLib\Entities\Relations\StorageField;
use RPLib\Entities\Traits\Entity;
use RPLib\Enums\TurnStatus;
use UnexpectedValueException;

/**
 * Class Turn
 * @package RPLib\Entities
 */
class Turn {
    use Entity;

    /**
     * @var Player
     */
    private $player;

    /**
     * @var int
     */
    private $status;

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
    public function addAttribute(Attribute $attribute) {
        if ($this->hasAttribute($attribute)) {
            throw new UnexpectedValueException("This attribute is already present.");
        }

        if (!GameManager::getInstance()->getRegistry()->turns->hasAttribute($attribute->getReference())) {
            throw new OutOfRangeException("This attribute can't be put on a turn.");
        }

        $this->attributes[] = $attribute;
    }

    /**
     * @param Statistic $statistic
     */
    public function addStatistic(Statistic $statistic) {
        if ($this->hasStatistic($statistic)) {
            throw new UnexpectedValueException("This statistic is already present.");
        }

        if (!GameManager::getInstance()->getRegistry()->turns->hasStatistic($statistic->getReference())) {
            throw new OutOfRangeException("This statistic can't be put on a turn.");
        }

        $this->statistics[] = $statistic;
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
     * @return int
     */
    public function getStatus() : int {
        return $this->status;
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

}