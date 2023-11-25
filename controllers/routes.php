<?php

require_once(ROOT . "/middleware/renderer.php");
require_once(ROOT . "/middleware/log.php");

function fromController(string $path) {
    return function(array $context) use ($path) {
        return (require(ROOT . "/controllers/" . $path . ".php"))($context);
    };
}

return function(Router $router) {
    $router->addRoute(GET, "/", fromController("/GET"));
    $router->addRoute(GET, "/test", useRenderer(fromController("/test/GET")));

    $router->addRoute(GET, "/ipaddress",
        useLog(
            useRenderer(
                fromController("/ipaddress/GET")
            ),
            "ipaddress"
        )
    );
    $router->addRoute(GET, "/whois",
        useLog(
            useRenderer(
                fromController("/whois/GET")
            ),
            "whois"
        )
    );

    $router->addRoute(GET, "/punycode",
        useLog(
            useRenderer(
                fromController("/punycode/GET")
            ),
            "punycode"
        )
    );
};
