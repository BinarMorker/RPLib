<?php

namespace RPLib\Entities\Relations;

/**
 * Class StorageField
 * @package RPLib\Entities\Relations
 */
class StorageField {

    /**
     * @var string
     */
    private $name;

    /**
     * @var callable
     */
    private $setter;

    /**
     * StorageField constructor.
     * @param string $name
     * @param callable $setter
     */
    public function __construct(string $name, callable $setter) {
        $this->name = $name;
        $this->setter = $setter;
    }

    /**
     * @return string
     */
    public function getName() : string {
        return $this->name;
    }

    /**
     * @return callable
     */
    public function getSetter() : callable {
        return $this->setter;
    }

}