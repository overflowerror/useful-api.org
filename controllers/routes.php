<?php

require_once(ROOT . "/middleware/renderer.php");

function fromController(string $path) {
    return function(array $context) use ($path) {
        return (require(ROOT . "/controllers/" . $path . ".php"))($context);
    };
}

return function(Router $router) {
    $router->addRoute(GET, "/", fromController("/GET"));
    $router->addRoute(GET, "/test", useRenderer(fromController("/test/GET")));

    $router->addRoute(GET, "/ipaddress", useRenderer(fromController("/ipaddress/GET")));
    $router->addRoute(GET, "/whois", useRenderer(fromController("/whois/GET")));

    $router->addRoute(GET, "/punycode", useRenderer(fromController("/punycode/GET")));
};
