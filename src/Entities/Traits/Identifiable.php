<?php

namespace RPLib\Entities\Traits;

use RPLib\Core\Storage;
use RPLib\Entities\Relations\StorageField;

/**
 * Trait Identifiable
 * @package RPLib\Entities\Traits
 */
trait Identifiable {

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $table;

    /**
     * Identifiable constructor.
     * @param string $table
     * @param int|null $id
     */
    private function __construct(string $table, int $id = null) {
        $this->table = $table;

        if (!is_null($id)) {
            $storage = Storage::getInstance();
            $results = $storage->query("SELECT `id`, `name` FROM `{$table}` WHERE `id` = {$id}");

            if (count($results) > 0) {
                $this->id = $results[0]['id'];
                $this->name = $results[0]['name'];
            }
        }
    }

    /**
     * @param array $fields
     */
    private function load(array $fields) {
        $storage = Storage::getInstance();
        $fieldNames = join('`, `', array_map(function($field) {
            if ($field instanceof StorageField) {
                return $field->getName();
            } elseif (is_array($field)) {
                return $field['name'];
            } else {
                return $field;
            }
        }, $fields));
        $results = $storage->query("SELECT `{$fieldNames}` FROM {$this->table} WHERE `id` = {$this->id}");

        if (count($results) > 0) {
            foreach ($fields as $field) {
                if ($field instanceof StorageField) {
                    $callable = $field->getSetter();

                    if (is_callable($callable)) {
                        $callable($results[0][$field->getName()]);
                    } else {
                        $this->{$field->getName()} = $results[0][$field->getName()];
                    }
                } elseif (is_array($field)) {
                    $callable = $field['callable'];

                    if (is_callable($callable)) {
                        $callable($results[0][$field['name']]);
                    } else {
                        $this->{$field['name']} = $results[0][$field['name']];
                    }
                } else {
                    $this->{$field} = $results[0][$field];
                }
            }
        }
    }

    /**
     * @param array $parameters
     * @return int
     */
    public function save(array $parameters) : int {
        $storage = Storage::getInstance();
        $columns = [];
        $values = [];

        foreach ($parameters as $key => $value) {
            $columns[] = "`{$key}`";

            if (is_int($value) || is_bool($value)) {
                $values[] = "{$value}";
            } else {
                $values[] = "'{$value}'";
            }
        }

        $columns = join(',', $columns);
        $values = join(',', $values);

        if ($this->id != null) {
            $values[] = $this->id;
            $columns[] = '`id`';
        }

        $storage->execute("REPLACE INTO `{$this->table}` ({$columns}) VALUES ({$values});");
        $this->id = $storage->getLastInsertId();
        return $this->id;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName() : string {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name) {
        $this->name = $name;
    }

}