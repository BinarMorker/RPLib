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
    public $games;

    /**
     * @var Registry
     */
    public $turns;

    /**
     * RegistryFactory constructor.
     */
    public function __construct() {
        $this->players = new Registry();
        $this->games = new Registry();
        $this->turns = new Registry();
    }

}