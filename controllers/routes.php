<?php

require_once(ROOT . "/router/Router.php");

require_once(ROOT . "/middleware/renderer.php");
require_once(ROOT . "/middleware/log.php");

function fromController(string $path, string $endpoint = null) {
    return function(array &$context) use ($path, $endpoint) {
        if ($endpoint)
            $context["endpoint"] = $endpoint;

        return (require(ROOT . "/controllers/" . $path . ".php"))($context);
    };
}

return function(Router $router) {
    $router->addRoute(GET, "/", fromController("/GET"));
    $router->addRoute(GET, "/test", useRenderer(fromController("/test/GET")));

    $apiRouter = new Router("");
    $router->addRoute(GET, "/.*",
        useLog(useRenderer($apiRouter))
    );

    $apiRouter->addRoute(GET, "/ipaddress",
        fromController("/ipaddress/GET", "ipaddress")
    );
    $apiRouter->addRoute(GET, "/whois",
        fromController("/whois/GET", "whois")
    );

    $apiRouter->addRoute(GET, "/punycode",
        fromController("/punycode/GET", "punycode")
    );
};
