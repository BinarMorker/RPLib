<?php

namespace RPLib\Entities\Relations;

use Exception;
use RPLib\Entities\Traits\Identifiable;
use RPLib\Enums\ValueType;
use RPLib\Core\Storage;

/**
 * Class StatisticReference
 * @package RPLib\Entities\Relations
 */
class StatisticReference {
    use Identifiable {
        Identifiable::__construct as private initialize;
        Identifiable::save as private __save;
    }

    /**
     * @var int
     */
    private $valueType;

    /**
     * StatisticReference constructor.
     * @param int|null $id
     */
    public function __construct(int $id = null) {
        $this->initialize('rplib_statistic_reference', $id);

        if (!is_null($id)) {
            $this->load([
                new StorageField('type', function($value) {
                    $this->setValueType($value);
                })
            ]);
        }
    }

    public static function create(string $name) : self {
        $storage = Storage::getInstance();
        $results = $storage->query("SELECT `id`, `name` FROM `rplib_statistic_reference` WHERE `name` = {$name}");

        if (count($results) > 0) {
            return new self($results[0]['id']);
        }

        throw new Exception("There was no attribute found by this name");
    }

    /**
     * @return int
     */
    public function getValueType() : int {
        return $this->valueType;
    }

    /**
     * @param int $valueType
     */
    public function setValueType(int $valueType) {
        switch ($valueType) {
            case ValueType::FLOAT:
            case ValueType::BOOLEAN:
            case ValueType::STRING:
            case ValueType::ARRAY:
            case ValueType::OBJECT:
            case ValueType::RESOURCE:
            case ValueType::INTEGER:
            case ValueType::NONE:
                $this->valueType = $valueType;
                break;
            case ValueType::UNKNOWN:
            default:
                $this->valueType = ValueType::UNKNOWN;
                break;
        }
    }

    /**
     *
     */
    public function save() {
        $this->__save([
            'name' => $this->getName(),
            'type' => $this->getValueType()
        ]);
    }
}