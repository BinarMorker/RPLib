<?php

namespace RPLib\Entities\Traits;

use Exception;
use ReflectionClass;
use RPLib\Core\Storage;
use RPLib\Entities\Attribute;
use RPLib\Entities\Interfaces\IIdentifiable;
use RPLib\Entities\Relations\LinkedEntity;
use RPLib\Entities\Statistic;

/**
 * Trait Entity
 * @package RPLib\Entities\Traits
 */
trait Entity {
    use Identifiable {
        Identifiable::__construct as private initialize;
        Identifiable::save as private __save;
    }

    /**
     * @var LinkedEntity
     */
    protected $attributeLink;

    /**
     * @var LinkedEntity
     */
    protected $statisticLink;

    /**
     * @var Attribute[]
     */
    private $attributes;

    /**
     * @var Statistic[]
     */
    private $statistics;

    /**
     * @return Attribute[]
     */
    public function getAttributes() : array {
        return $this->attributes;
    }

    /**
     * @param string $attributeName
     * @return Attribute[]
     */
    public function getAttributesByName(string $attributeName) : array {
        $filter = array_filter($this->attributes, function(Attribute $item) use ($attributeName) {
            return $item->getName() == $attributeName;
        });

        return $filter;
    }

    /**
     * @param string $attributeName
     * @return bool
     */
    public function hasAttribute(string $attributeName) : bool {
        return count($this->getAttributesByName($attributeName)) > 0;
    }

    /**
     * @param string $attributeName
     * @return Attribute
     */
    public function getAttribute(string $attributeName) : Attribute {
        $attributes = $this->getAttributesByName($attributeName);
        return count($attributes) > 0 ? $attributes[0] : null;
    }

    /**
     * @param Attribute $attribute
     */
    private function setAttribute(Attribute $attribute) {
        $hasAttribute = $this->hasAttribute($attribute->getName());

        if ($hasAttribute) {
            $entityAttributes = $this->attributes;
            $this->attributes = [];

            foreach ($entityAttributes as $entityAttribute) {
                if ($entityAttribute->getName() == $attribute->getName()) {
                    $this->attributes[] = $attribute;
                } else {
                    $this->attributes[] = $entityAttribute;
                }
            }
        } else {
            $this->attributes[] = $attribute;
        }
    }

    /**
     * @return Statistic[]
     */
    public function getStatistics() : array {
        return $this->statistics;
    }

    /**
     * @param string $statisticName
     * @return Statistic[]
     */
    public function getStatisticsByName(string $statisticName) : array {
        $filter = array_filter($this->statistics, function(Statistic $item) use ($statisticName) {
            return $item->getName() == $statisticName;
        });

        return $filter;
    }

    /**
     * @param string $statisticName
     * @return bool
     */
    public function hasStatistic(string $statisticName) : bool {
        return count($this->getStatisticsByName($statisticName)) > 0;
    }

    /**
     * @param string $statisticName
     * @return Statistic
     */
    public function getStatistic(string $statisticName) : Statistic {
        $statistics = $this->getStatisticsByName($statisticName);
        return count($statistics) > 0 ? $statistics[0] : null;
    }

    /**
     * @param Statistic $statistic
     */
    private function setStatistic(Statistic $statistic) {
        $hasStatistic = $this->hasStatistic($statistic->getName());

        if ($hasStatistic) {
            $entityStatistics = $this->statistics;
            $this->statistics = [];

            foreach ($entityStatistics as $entityStatistic) {
                if ($entityStatistic->getName() == $statistic->getName()) {
                    $this->statistics[] = $statistic;
                } else {
                    $this->statistics[] = $entityStatistic;
                }
            }
        } else {
            $this->statistics[] = $statistic;
        }
    }

    /**
     * @param LinkedEntity $entity
     * @return array
     * @throws Exception
     */
    private function loadLinked(LinkedEntity $entity) : array {
        $storage = Storage::getInstance();
        $results = $storage->query("SELECT `{$entity->getTargetField()}` FROM {$entity->getTableName()} WHERE `{$entity->getSourceField()}` = {$this->id}");
        $objects = [];

        try {
            $class = new ReflectionClass($entity->getTargetEntity());

            if (count($results) > 0) {
                foreach ($results as $result) {
                    $objects[] = $class->newInstance($result[$entity->getTargetField()]);
                }
            }
        } catch (Exception $e) {
            throw $e;
        }

        return $objects;
    }

    /**
     * @param LinkedEntity $entity
     * @return array
     * @throws Exception
     */
    private function loadOrdered(LinkedEntity $entity) : array {
        $storage = Storage::getInstance();
        $results = $storage->query("SELECT `{$entity->getTargetField()}`, `position` FROM {$entity->getTableName()} WHERE `{$entity->getSourceField()}` = {$this->id}");
        $objects = [];

        try {
            $class = new ReflectionClass($entity->getTargetEntity());

            if (count($results) > 0) {
                foreach ($results as $result) {
                    $objects[$result['position']] = $class->newInstance($result[$entity->getTargetField()]);
                }
            }
        } catch (Exception $e) {
            throw $e;
        }

        ksort($objects);
        return $objects;
    }

    /**
     * @param LinkedEntity $link
     * @param IIdentifiable $target
     */
    private function saveLinked(LinkedEntity $link, IIdentifiable $target) {
        $storage = Storage::getInstance();
        $storage->execute("REPLACE INTO `{$link->getTableName()}` ({$link->getSourceField()}, {$link->getTargetField()}) VALUES ({$this->id}, {$target->getId()})");
    }

    /**
     * @param LinkedEntity $link
     * @param IIdentifiable $target
     * @param int $position
     */
    private function saveOrdered(LinkedEntity $link, IIdentifiable $target, int $position) {
        $storage = Storage::getInstance();
        $storage->execute("REPLACE INTO `{$link->getTableName()}` ({$link->getSourceField()}, {$link->getTargetField()}, position) VALUES ({$this->id}, {$target->getId()}, {$position})");
    }

    /**
     * @param LinkedEntity $link
     */
    private function deleteLinked(LinkedEntity $link) {
        $storage = Storage::getInstance();
        $storage->execute("DELETE FROM `{$link->getTableName()}` WHERE {$link->getSourceField()} = {$this->id}");
    }

    /**
     * @param array $parameters
     */
    public function save(array $parameters) {
        $this->__save($parameters);

        foreach ($this->attributes as $attribute) {
            $attribute->save();
            $this->saveLinked($this->attributeLink, $attribute);
        }

        foreach ($this->statistics as $statistic) {
            $statistic->save();
            $this->saveLinked($this->statisticLink, $statistic);
        }
    }
}