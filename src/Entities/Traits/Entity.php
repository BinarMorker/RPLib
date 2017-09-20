<?php

namespace RPLib\Entities\Traits;

use Exception;
use ReflectionClass;
use RPLib\Core\Storage;
use RPLib\Entities\Attribute;
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
     * @param Attribute $attribute
     * @return bool
     */
    public function hasAttribute(Attribute $attribute) : bool {
        $filter = array_filter($this->attributes, function(Attribute $item) use ($attribute) {
            return $item->getName() == $attribute->getName();
        });

        return count($filter) > 0;
    }

    /**
     * @return Statistic[]
     */
    public function getStatistics() : array {
        return $this->statistics;
    }

    /**
     * @param Statistic $statistics
     * @return bool
     */
    public function hasStatistic(Statistic $statistics) : bool {
        $filter = array_filter($this->statistics, function(Statistic $item) use ($statistics) {
            return $item->getName() == $statistics->getName();
        });

        return count($filter) > 0;
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
                    $objects[] = $class->newInstance($result);
                }
            }
        } catch (Exception $e) {
            throw $e;
        }

        return $objects;
    }

    /**
     * @param LinkedEntity $link
     * @param Identifiable $target
     */
    private function saveLinked(LinkedEntity $link, Identifiable $target) {
        $storage = Storage::getInstance();
        $storage->execute("REPLACE INTO `{$link->getTableName()}` ({$link->getSourceField()}, {$link->getTargetField()}) VALUES ({$this->id}, {$target->getId()})");
    }

    /**
     * @param array $parameters
     */
    public function save(array $parameters) {
        $this->__save($parameters);

        foreach ($this->attributes as $attribute) {
            $this->saveLinked($this->attributeLink, $attribute);
        }

        foreach ($this->statistics as $statistic) {
            $this->saveLinked($this->statisticLink, $statistic);
        }
    }
}