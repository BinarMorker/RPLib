<?php

namespace RPLib\Entities\Relations;

/**
 * Class VersionedEntity
 * @package RPLib\Entities\Relations
 */
class VersionedEntity {

    /**
     * @var string
     */
    private $tableName;

    /**
     * @var string
     */
    private $identifierField;

    /**
     * LinkedEntity constructor.
     * @param string $tableName
     * @param string $identifierField
     */
    public function __construct(string $tableName, string $identifierField) {
        $this->tableName = $tableName;
        $this->identifierField = $identifierField;
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
    public function getIdentifierField() : string {
        return $this->identifierField;
    }

}