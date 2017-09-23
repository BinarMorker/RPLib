<?php

namespace RPLib\Entities\Interfaces;

interface IIdentifiable {

    public function save();

    public function getId();

    public function setId(int $id);

    public function getName() : string;

    public function setName(string $name);

}