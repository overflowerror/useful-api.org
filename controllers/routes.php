<?php

require_once(ROOT . "/router/Router.php");

require_once(ROOT . "/middleware/renderer.php");
require_once(ROOT . "/middleware/log.php");
require_once(ROOT . "/middleware/ratelimit.php");

function fromController(string $path, string $endpoint = null) {
    return function(array &$context) use ($path, $endpoint) {
        if ($endpoint)
            $context[ENDPOINT] = $endpoint;

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
        useRateLimit(
            fromController("/whois/GET", "whois"),
            [
                RATELIMIT_ENDPOINT => "whois",
                RATELIMIT_AMOUNT_PER_IP => 1,
                RATELIMIT_AMOUNT_PER_KEY => 10,
                RATELIMIT_TIMEFRAME => 60,
            ]
        )
    );

    $apiRouter->addRoute(GET, "/punycode",
        fromController("/punycode/GET", "punycode")
    );
};
