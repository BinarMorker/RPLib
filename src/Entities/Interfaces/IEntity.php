<?php

namespace RPLib\Entities\Interfaces;

use RPLib\Entities\Attribute;
use RPLib\Entities\Statistic;

interface IEntity extends IIdentifiable {

    public function getAttributes() : array;

    public function getAttributesByName(string $attributeName) : array;

    public function hasAttribute(string $attributeName) : bool;

    public function setAttribute(Attribute $attribute);

    public function getAttribute(string $attributeName) : Attribute;

    public function getStatistics() : array;

    public function getStatisticsByName(string $statisticName) : array;

    public function hasStatistic(string $statisticName) : bool;

    public function setStatistic(Statistic $statistic);

    public function getStatistic(string $statisticName) : Statistic;

}