<?php

namespace RPLib\Entities;

use OutOfRangeException;
use RPLib\Core\GameManager;
use RPLib\Entities\Interfaces\IEntity;
use RPLib\Entities\Relations\LinkedEntity;
use RPLib\Entities\Traits\Entity;
use UnexpectedValueException;

/**
 * Class Player
 * @package RPLib\Entities
 */
class Player implements IEntity {
    use Entity {
        Entity::save as private saveParameters;
        Entity::setAttribute as private __setAttribute;
        Entity::setStatistic as private __setStatistic;
    }

    /**
     * Player constructor.
     * @param int|null $id
     */
    public function __construct(int $id = null) {
        $this->initialize('rplib_player', $id);

        $this->attributeLink = new LinkedEntity('rplib_player_attributes', 'player', 'attribute', Attribute::class);
        $this->statisticLink = new LinkedEntity('rplib_player_statistics', 'player', 'statistic', Statistic::class);

        if (!is_null($this->id)) {
            $this->attributes = $this->loadLinked($this->attributeLink);
            $this->statistics = $this->loadLinked($this->statisticLink);
        } else {
            $this->attributes = [];
            $this->statistics = [];
        }
    }

    /**
     * @param Attribute $attribute
     */
    public function setAttribute(Attribute $attribute) {
        if ($this->hasAttribute($attribute->getName())) {
            throw new UnexpectedValueException("The attribute \"{$attribute->getName()}\" is already present.");
        }

        if (!GameManager::getInstance()->getRegistry()->players->hasAttribute($attribute->getReference())) {
            throw new OutOfRangeException("The attribute \"{$attribute->getName()}\" can't be put on a player.");
        }

        $this->__setAttribute($attribute);
    }

    /**
     * @param Statistic $statistic
     */
    public function setStatistic(Statistic $statistic) {
        if ($this->hasStatistic($statistic->getName())) {
            throw new UnexpectedValueException("The statistic \"{$statistic->getName()}\" is already present.");
        }

        if (!GameManager::getInstance()->getRegistry()->players->hasStatistic($statistic->getReference())) {
            throw new OutOfRangeException("The statistic \"{$statistic->getName()}\" can't be put on a player.");
        }

        $this->__setStatistic($statistic);
    }

    public function save() {
        $this->saveParameters([
            'name' => $this->getName()
        ]);
    }
}