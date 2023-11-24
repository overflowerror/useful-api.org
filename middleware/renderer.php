<?php

require_once(ROOT . "/utils/arrays.php");

function useRenderer($handler, string $default = "JSON", string $query_param = "format") {
    return function (array $context) use ($handler, $default, $query_param) {
        $rendererMap = require(ROOT . "/renderer/renderer.php");

        $renderer = $rendererMap[strtoupper($_GET[$query_param] ?? "")] ?? $rendererMap[$default];
        $context["renderer"] = $renderer;

        $result = $handler($context);
        if ($result !== null) {
            $renderer($result);
        }

        return $result;
    };
}