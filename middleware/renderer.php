<?php

function useRenderer($handler, string $default, string $query_param = "format") {
    return function (array $context) use ($handler, $default, $query_param) {
        $rendererMap = require(ROOT . "/renderer/renderer.php");

        $format = array_default($query_param, $_GET);
        if (!$format || !key_exists(strtoupper($format), $rendererMap)) {
            $format = $default;
        }


        $context["renderer"] = $rendererMap[strtoupper($format)];

        $handler($context);
    };
}