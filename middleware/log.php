<?php

require_once(ROOT . "/persistence/models/LogEntry.php");

function useLog($handler, string $endpoint = "") {
    return function (array &$context) use ($handler, $endpoint) {
        $context["endpoint"] = $endpoint;

        $result = $handler($context);

        $accessKey = $context["ACCESS_KEY"] ?? "";

        $entry = new LogEntry(
            $context["endpoint"],
            $_SERVER['REMOTE_ADDR'],
            $accessKey,
        );
        $context[REPOSITORIES]->logs()->add($entry);

        return $result;
    };
}