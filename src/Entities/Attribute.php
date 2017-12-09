<?php

namespace RPLib\Entities;

use RPLib\Entities\Interfaces\IVersionable;
use RPLib\Entities\Relations\AttributeReference;
use RPLib\Entities\Relations\StorageField;
use RPLib\Entities\Relations\VersionedEntity;
use RPLib\Entities\Traits\Versionable;
use RPLib\Enums\ValueType;
use RPLib\Enums\VersionType;
use UnexpectedValueException;

/**
 * Class Attribute
 * @package RPLib\Entities
 */
class Attribute implements IVersionable {
    use Versionable;

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
        $this->versionLink = new VersionedEntity('rplib_attribute_version', 'attribute');
        $this->version = 0;

        if (!is_null($id)) {
            $this->load([
                new StorageField('reference', function($value) {
                    $this->setReference(new AttributeReference($value));
                }),
                new StorageField('version', function($value) {
                    $this->setCurrentVersion($value);
                })
            ]);
            $this->value = $this->loadVersion($this->versionLink, VersionType::NONE, $this->getCurrentVersion());
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
        $this->setCurrentVersion(0);
    }

    /**
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * @return array
     */
    public function getVersions() : array {
        return $this->loadVersions($this->versionLink);
    }

    /**
     *
     */
    public function save() {
        if ($this->reference == null) {
            throw new UnexpectedValueException("The reference for the attribute \"{$this->getName()}\" does not exist");
        }

        if ($this->getCurrentVersion() <= 0) {
            $this->__save([
                'name' => $this->getName(),
                'reference' => $this->getReference()->getId(),
                'version' => $this->getVersionNumber($this->versionLink, VersionType::LATEST) + 1
            ]);
        } else {
            $this->__save([
                'name' => $this->getName(),
                'reference' => $this->getReference()->getId(),
                'version' => $this->getCurrentVersion()
            ]);
        }

        $this->saveVersion($this->versionLink, $this->getValue(), $this->getCurrentVersion());
    }
}