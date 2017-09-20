<?php

namespace RPLib\Entities\Relations;

/**
 * Class LinkedEntity
 * @package RPLib\Entities\Relations
 */
class LinkedEntity {

    /**
     * @var string
     */
    private $tableName;

    /**
     * @var string
     */
    private $sourceField;

    /**
     * @var string
     */
    private $targetField;

    /**
     * @var string
     */
    private $targetEntity;

    /**
     * LinkedEntity constructor.
     * @param string $tableName
     * @param string $sourceField
     * @param string $targetField
     * @param string|null $targetEntity
     */
    public function __construct(string $tableName, string $sourceField, string $targetField, string $targetEntity = null) {
        $this->tableName = $tableName;
        $this->sourceField = $sourceField;
        $this->targetField = $targetField;
        $this->targetEntity = $targetEntity;
    }

    /**
     * @return string
     */
    public function getTableName() : string {
        return $this->tableName;
    }

    /**
     * @return string
     */
    public function getSourceField() : string {
        return $this->sourceField;
    }

    /**
     * @return string
     */
    public function getTargetField() : string {
        return $this->targetField;
    }

    /**
     * @return string
     */
    public function getTargetEntity() : string {
        return $this->targetEntity;
    }

}