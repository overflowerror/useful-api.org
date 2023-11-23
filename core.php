<?php

const ROOT = __DIR__;

define("MAINTENANCE_MODE", require(ROOT . "/maintenance.php"));

if (MAINTENANCE_MODE) {
    require(ROOT . "./templates/maintenance.php");
} else {
    $router = require(ROOT . "/router/Router.php");

    (require(ROOT . "/controllers/routes.php"))($router);

    $router->execute();
}
