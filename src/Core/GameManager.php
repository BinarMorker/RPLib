<?php

namespace RPLib\Core;

use RPLib\Entities\Game;
use RPLib\Registry\RegistryFactory;

/**
 * Class GameManager
 * @package RPLib
 */
class GameManager {

    /**
     * @var RegistryFactory
     */
    private $registry;

    /**
     * @var static
     */
    private static $instance;

    /**
     * @var Game[]
     */
    private $games;

    /**
     * Protected constructor to prevent creating a new instance of the class.
     */
    protected function __construct() {
        $this->registry = new RegistryFactory();
        $this->games = [];
    }

    /**
     * Private clone method to prevent cloning of the instance.
     * @return void
     */
    protected function __clone() {

    }

    /**
     * Private unserialize method to prevent unserializing.
     * @return void
     */
    protected function __wakeup() {

    }

    /**
     * @return GameManager
     */
    public static function getInstance() : GameManager {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * @return RegistryFactory
     */
    public function getRegistry() : RegistryFactory {
        return $this->registry;
    }

    /**
     * @return Game[]
     */
    public function getGames() : array {
        return $this->games;
    }

    /**
     * @param Game $game
     */
    public function addGame(Game $game) {
        $this->games[] = $game;
    }

}