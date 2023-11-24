<?php

return function (array $context) {
    if (key_exists("to", $_GET)) {
        $context["renderer"](idn_to_ascii($_GET["to"]));
    } elseif (key_exists("from", $_GET)) {
        $context["renderer"](idn_to_utf8($_GET["from"]));
    } else {
        setStatusCode(400);
        $context["renderer"](errorResponse("Unknown mode", "Please specify one of the following query parameters: to, from"));
    }
};
