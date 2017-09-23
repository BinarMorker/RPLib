<?php

namespace RPLib\Entities;

use RPLib\Entities\Interfaces\IIdentifiable;
use RPLib\Entities\Relations\AttributeReference;
use RPLib\Entities\Relations\StorageField;
use RPLib\Entities\Traits\Identifiable;
use RPLib\Enums\ValueType;
use UnexpectedValueException;

/**
 * Class Attribute
 * @package RPLib\Entities
 */
class Attribute implements IIdentifiable {
    use Identifiable {
        Identifiable::__construct as private initialize;
        Identifiable::save as private __save;
    }

    /**
     * @var AttributeReference
     */
    private $reference;

    /**
     * @var mixed
     */
    private $value;

    /**
     * Attribute constructor.
     * @param int|null $id
     */
    public function __construct(int $id = null) {
        $this->initialize('rplib_attribute', $id);

        if (!is_null($id)) {
            $this->load([
                new StorageField('reference', function($value) {
                    $this->setReference(new AttributeReference($value));
                }),
                new StorageField('type', function($value) {
                    $this->setValue(unserialize($value));
                })
            ]);
        }
    }

    /**
     * @param AttributeReference $reference
     */
    public function setReference(AttributeReference $reference) {
        $this->reference = $reference;
    }

    /**
     * @return AttributeReference
     */
    public function getReference() : AttributeReference {
        return $this->reference;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value) {
        if ($this->reference->getValueType() == ValueType::UNKNOWN) {
            throw new UnexpectedValueException("There is no value type set");
        }

        if ($this->reference->getValueType() == ValueType::NONE) {
            throw new UnexpectedValueException("There is no value expected");
        }

        $type = gettype($value);

        switch ($type) {
            case "boolean":
                if ($this->reference->getValueType() != ValueType::BOOLEAN) {
                    throw new UnexpectedValueException("Boolean value expected, got {$type}");
                }

                break;
            case "integer":
                if ($this->reference->getValueType() != ValueType::INTEGER) {
                    throw new UnexpectedValueException("Integer value expected, got {$type}");
                }

                break;
            case "double":
                if ($this->reference->getValueType() != ValueType::FLOAT) {
                    throw new UnexpectedValueException("Floating point value expected, got {$type}");
                }

                break;
            case "string":
                if ($this->reference->getValueType() != ValueType::STRING) {
                    throw new UnexpectedValueException("String value expected, got {$type}");
                }

                break;
            case "array":
                if ($this->reference->getValueType() != ValueType::ARRAY) {
                    throw new UnexpectedValueException("Array of values expected, got {$type}");
                }

                break;
            case "object":
                if ($this->reference->getValueType() != ValueType::OBJECT) {
                    throw new UnexpectedValueException("Object expected, got {$type}");
                }

                break;
            case "resource":
                if ($this->reference->getValueType() != ValueType::RESOURCE) {
                    throw new UnexpectedValueException("Resource expected, got {$type}");
                }

                break;
            case "NULL":
            case "unknown type":
            default:
                throw new UnexpectedValueException("Unexpected value, got {$type}");
                break;
        }

        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }

    /**
     *
     */
    public function save() {
        if ($this->reference == null) {
            throw new UnexpectedValueException("The reference for this attribute does not exist");
        }

        $this->__save([
            'name' => $this->getName(),
            'reference' => $this->getReference()->getId(),
            'value' => serialize($this->getValue())
        ]);
    }
}