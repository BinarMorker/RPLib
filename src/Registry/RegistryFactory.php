<?php

namespace RPLib\Registry;

/**
 * Class RegistryFactory
 * @package RPLib\Registry
 */
class RegistryFactory {

    /**
     * @var Registry
     */
    public $players;

    /**
     * @var Registry
     */
    public $characters;

    /**
     * @var Registry
     */
    public $turns;

    /**
     * RegistryFactory constructor.
     */
    public function __construct() {
        $this->players = new Registry();
        $this->characters = new Registry();
        $this->turns = new Registry();
    }

}