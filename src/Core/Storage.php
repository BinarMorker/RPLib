<?php

namespace RPLib\Core;

use Exception;
use ReflectionClass;

/**
 * Class Storage
 * @package RPLib
 */
class Storage {

    /**
     * @var mixed
     */
    private $connection;

    /**
     * @var static
     */
    private static $instance;

    /**
     * Protected constructor to prevent creating a new instance of the class.
     */
    protected function __construct() {
        try {
            if (!is_file("config.ini")) {
                throw new Exception("Could not find a configuration");
            }

            $apineLoader = new ReflectionClass("\Apine\Autoloader");
            $loader = $apineLoader->newInstance();
            $loader->register();

            $apineConfig = new ReflectionClass("\Apine\Core\Config");
            $config = $apineConfig->newInstance("config.ini");

            $apineDatabase = new ReflectionClass("\Apine\Core\Database");
            $this->connection = $apineDatabase->newInstanceArgs([
                $config->get('database', 'type'),
                $config->get('database', 'host'),
                $config->get('database', 'dbname'),
                $config->get('database', 'username'),
                $config->get('database', 'password'),
                $config->get('database', 'charset')
            ]);
        } catch (Exception $e) {
            throw $e;
        }

        // TODO: Support more database wrappers
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
     * @return Storage
     */
    public static function getInstance() : Storage {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * @return \Apine\Core\Database|mixed|null
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * @param string $query
     * @return array
     * @throws Exception
     */
    public function query(string $query) : array {
        $connection = self::getInstance()->getConnection();

        switch (get_class($connection)) {
            case 'Apine\Core\Database':
                return $connection->select($query);
                break;
            default:
                throw new Exception("No database wrapper");
                break;
        }
    }

    /**
     * @param string $query
     * @throws Exception
     */
    public function execute(string $query) {
        $connection = self::getInstance()->getConnection();

        switch (get_class($connection)) {
            case 'Apine\Core\Database':
                $connection->exec($query);
                break;
            default:
                throw new Exception("No database wrapper");
                break;
        }
    }

    public function getLastInsertId() {
        $connection = self::getInstance()->getConnection();

        switch (get_class($connection)) {
            case 'Apine\Core\Database':
                return $connection->last_insert_id();
                break;
            default:
                throw new Exception("No database wrapper");
                break;
        }
    }

    public function volatileTransaction(callable $method) {
        $connection = self::getInstance()->getConnection();

        switch (get_class($connection)) {
            case 'Apine\Core\Database':
                try {
                    $connection->open_transaction();
                    $method();
                    $connection->rollback_transaction();
                } catch (Exception $e) {
                    $connection->rollback_transaction();
                    throw $e;
                }

                break;
            default:
                throw new Exception("No database wrapper");
                break;
        }
    }

}