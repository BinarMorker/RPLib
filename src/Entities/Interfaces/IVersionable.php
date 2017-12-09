<?php

namespace RPLib\Entities\Interfaces;

use RPLib\Entities\Relations\VersionedEntity;

interface IVersionable extends IIdentifiable {

    public function getOriginalVersion();

    public function getLatestVersion();

    public function getVersion(int $version);

    public function getVersions() : array;

    public function getCurrentVersion() : int;

    public function setCurrentVersion(int $version);

}