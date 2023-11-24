<?php

const ROOT = __DIR__;

define("MAINTENANCE_MODE", require(ROOT . "/maintenance.php"));

if (MAINTENANCE_MODE) {
    require(ROOT . "/templates/maintenance.php");
} else {
    $connection = require_once(ROOT . "/persistence/connection.php");
    (require(ROOT . "/persistence/migrate.php"))($connection);

    $router = require(ROOT . "/router/Router.php");
    (require(ROOT . "/controllers/routes.php"))($router);
    $router->execute([
        "DB_CONNECTION" => $connection,
    ]);
}
