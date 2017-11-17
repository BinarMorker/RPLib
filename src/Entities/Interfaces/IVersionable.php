<?php

namespace RPLib\Entities\Interfaces;

interface IVersionable extends IIdentifiable {

    public function getOriginalVersion();

    public function getLatestVersion();

    public function getVersion(int $version);

    public function getCurrentVersion() : int;

    public function setCurrentVersion(int $version);

}