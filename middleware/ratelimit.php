<?php

require_once(ROOT . "/utils/error.php");

const RATELIMIT_ENDPOINT = "RATELIMIT_ENDPOINT";
const RATELIMIT_TIMEFRAME = "RATELIMIT_TIMEFRAME";
const RATELIMIT_AMOUNT_PER_IP = "RATELIMIT_AMOUNT_PER_IP";
const RATELIMIT_AMOUNT_PER_KEY = "RATELIMIT_AMOUNT_PER_KEY";

function useRateLimit($handler, array $settings) {
    return function (array &$context) use ($handler, $settings) {

        $clientAddress = $_SERVER['REMOTE_ADDR'];
        $accessKey = $context["ACCESS_KEY"] ?? "";

        $endpoint = $settings[RATELIMIT_ENDPOINT];
        $timeframe = $settings[RATELIMIT_TIMEFRAME];

        $rateLimitReached = false;

        if ($accessKey && key_exists(RATELIMIT_AMOUNT_PER_KEY, $settings)) {
            $numberOfRequests = $context[REPOSITORIES]->logs()->countWithKeyInTimeframe(
                $endpoint,
                $accessKey,
                $timeframe
            );

            $rateLimitReached = $numberOfRequests > $settings[RATELIMIT_AMOUNT_PER_KEY];
        } else if(key_exists(RATELIMIT_AMOUNT_PER_IP, $settings)) {
            $numberOfRequests = $context[REPOSITORIES]->logs()->countWithAddressInTimeframe(
                $endpoint,
                $clientAddress,
                $timeframe
            );

            $rateLimitReached = $numberOfRequests > $settings[RATELIMIT_AMOUNT_PER_IP];
        }

        if ($rateLimitReached) {
            setStatusCode(429);
            return errorResponse("Rate limit reached", "Please wait before retrying.");
        } else {
            return $handler($context);
        }
    };
}