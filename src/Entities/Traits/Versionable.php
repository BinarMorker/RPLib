<?php

namespace RPLib\Entities\Traits;

use Exception;
use RPLib\Core\Storage;
use RPLib\Core\SerializationHelper;
use RPLib\Entities\Relations\VersionedEntity;
use RPLib\Enums\VersionType;
use UnexpectedValueException;

/**
 * Trait Versionable
 * @package RPLib\Entities\Traits
 */
trait Versionable {
    use Identifiable {
        Identifiable::__construct as private initialize;
        Identifiable::save as private __save;
    }

    /**
     * @var VersionedEntity
     */
    protected $versionLink;

    /**
     * @var int
     */
    private $version;

    /**
     * @param int $version
     */
    public function setCurrentVersion(int $version) {
        $this->version = $version;
    }

    /**
     * @return int
     */
    public function getCurrentVersion() : int {
        return $this->version;
    }

    /**
     * @return mixed
     */
    public function getOriginalVersion() {
        return $this->loadVersion($this->versionLink, VersionType::ORIGINAL);
    }

    /**
     * @return mixed
     */
    public function getLatestVersion() {
        return $this->loadVersion($this->versionLink, VersionType::LATEST);
    }

    /**
     * @param int $version
     * @return mixed
     */
    public function getVersion(int $version) {
        return $this->loadVersion($this->versionLink, VersionType::NONE, $version);
    }

    /**
     * @param VersionedEntity $entity
     * @return array
     * @throws Exception
     */
    private function loadVersions(VersionedEntity $entity) : array {
        $storage = Storage::getInstance();
        $response = [];

        $query = "SELECT `value`, `version` 
                  FROM {$entity->getTableName()} 
                  WHERE `{$entity->getIdentifierField()}` = {$this->id}";
        $results = $storage->query($query);

        try {
            if (count($results) > 0) {
                foreach ($results as $result) {
                    $response[] = [
                        "version" => $result['version'],
                        "value" => SerializationHelper::unserialize($result['value'])
                    ];
                }
            }
        } catch (Exception $e) {
            throw $e;
        }

        return $response;
    }

    /**
     * @param VersionedEntity $entity
     * @param int $type
     * @param int $version
     * @return mixed
     * @throws Exception
     */
    private function loadVersion(VersionedEntity $entity, int $type = VersionType::NONE, int $version = 0) {
        $storage = Storage::getInstance();
        $response = null;

        if ($version <= 0) {
            $version = $this->getVersionNumber($entity, $type);

            switch ($type) {
                case VersionType::LATEST:
                case VersionType::ORIGINAL:
                    break;
                case VersionType::NONE:
                default:
                    throw new UnexpectedValueException("Version must be set");
                    break;
            }
        }

        $query = "SELECT `value` 
                  FROM {$entity->getTableName()} 
                  WHERE `{$entity->getIdentifierField()}` = {$this->id} 
                  AND `version` = {$version}";
        $results = $storage->query($query);

        try {
            if (count($results) == 1) {
                $response = SerializationHelper::unserialize($results[0]['value']);
            }
        } catch (Exception $e) {
            throw $e;
        }

        return $response;
    }

    /**
     * @param VersionedEntity $link
     * @param mixed $value
     * @param int $version
     */
    private function saveVersion(VersionedEntity $link, $value, int $version = 0) {
        $storage = Storage::getInstance();
        $serializedValue = addslashes(serialize($value));

        if ($version <= 0) {
            $version = $this->getVersionNumber($link, VersionType::LATEST) + 1;
        }

        $query = "REPLACE INTO `{$link->getTableName()}` ({$link->getIdentifierField()}, `version`, `value`) 
                  VALUES ({$this->id}, {$version}, '{$serializedValue}')";
        $storage->execute($query);
    }

    /**
     * @param VersionedEntity $link
     * @param int $type
     * @return int
     * @throws Exception
     */
    private function getVersionNumber(VersionedEntity $link, int $type = VersionType::NONE) : int {
        $storage = Storage::getInstance();
        $response = 0;

        if (!is_null($this->id)) {
            switch ($type) {
                case VersionType::LATEST:
                    $query = "SELECT MAX(`version`) AS `v` 
                          FROM {$link->getTableName()} 
                          WHERE `{$link->getIdentifierField()}` = {$this->id}";
                    break;
                case VersionType::ORIGINAL:
                    $query = "SELECT MIN(`version`) AS `v`
                          FROM {$link->getTableName()} 
                          WHERE `{$link->getIdentifierField()}` = {$this->id}";
                    break;
                case VersionType::NONE:
                default:
                    throw new UnexpectedValueException("Version must be set");
                    break;
            }

            $results = $storage->query($query);

            try {
                if (count($results) == 1) {
                    $response = $results[0]['v'];

                    if (is_null($response)) {
                        $response = 0;
                    }
                }
            } catch (Exception $e) {
                throw $e;
            }
        }

        return $response;
    }
}