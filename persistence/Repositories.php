<?php

require_once(__DIR__ . "/repositories/Logs.php");

class Repositories {
    private $map = [];

    public function __construct(PDO $connection) {
        $this->map[Logs::class] = new Logs($connection);
    }

    public function logs(): Logs {
        return $this->map[Logs::class];
    }
}

return function(PDO $connection): Repositories {
    return new Repositories($connection);
};