<?php

namespace RPLib\Registry;

use RPLib\Entities\Relations\AttributeReference;
use RPLib\Entities\Relations\StatisticReference;
use UnexpectedValueException;

/**
 * Class Registry
 * @package RPLib\Registry
 */
class Registry {

    /**
     * @var AttributeReference[]
     */
    private $attributeReferences;

    /**
     * @var StatisticReference[]
     */
    private $statisticReferences;

    /**
     * Registry constructor.
     */
    public function __construct() {
        $this->attributeReferences = [];
        $this->statisticReferences = [];
    }

    /**
     * @param AttributeReference $reference
     */
    public function addAttribute(AttributeReference $reference) {
        if ($this->hasAttribute($reference)) {
            throw new UnexpectedValueException("The attribute \"{$reference->getName()}\" is already present.");
        }

        $this->attributeReferences[] = $reference;
    }

    /**
     * @param AttributeReference $reference
     * @return bool
     */
    public function hasAttribute(AttributeReference $reference) : bool {
        $filter = array_filter($this->attributeReferences, function(AttributeReference $item) use ($reference) {
            return $item->getName() == $reference->getName();
        });

        return count($filter) > 0;
    }

    /**
     * @param string $referenceName
     * @return AttributeReference
     */
    public function getAttribute(string $referenceName) : AttributeReference {
        $filter = array_filter($this->attributeReferences, function(AttributeReference $item) use ($referenceName) {
            return $item->getName() == $referenceName;
        });

        return count($filter) > 0 ? $filter[0] : $filter;
    }

    /**
     * @return AttributeReference[]
     */
    public function getAttributes() : array {
        return $this->attributeReferences;
    }

    /**
     * @param StatisticReference $reference
     */
    public function addStatistic(StatisticReference $reference) {
        if ($this->hasStatistic($reference)) {
            throw new UnexpectedValueException("The statistic \"{$reference->getName()}\" is already present.");
        }

        $this->statisticReferences[] = $reference;
    }

    /**
     * @param StatisticReference $reference
     * @return bool
     */
    public function hasStatistic(StatisticReference $reference) : bool {
        $filter = array_filter($this->statisticReferences, function(StatisticReference $item) use ($reference) {
            return $item->getName() == $reference->getName();
        });

        return count($filter) > 0;
    }

    /**
     * @return StatisticReference[]
     */
    public function getStatistics() : array {
        return $this->statisticReferences;
    }

}