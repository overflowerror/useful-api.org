<?php

require_once(ROOT . "/persistence/models/LogEntry.php");

const ENDPOINT = "ENDPOINT";
const ENDPOINT_DETAILS = "ENDPOINT_DETAILS";

function useLog($handler, string $endpoint = "") {
    return function (array &$context) use ($handler, $endpoint) {
        $context[ENDPOINT] = $endpoint;
        $context[ENDPOINT_DETAILS] = "";

        $result = $handler($context);

        $accessKey = $context["ACCESS_KEY"] ?? "";

        $entry = new LogEntry(
            $context[ENDPOINT],
            $context[ENDPOINT_DETAILS],
            $_SERVER['REMOTE_ADDR'],
            $accessKey,
        );
        $context[REPOSITORIES]->logs()->add($entry);

        return $result;
    };
}