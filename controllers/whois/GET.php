<?php

require_once(__DIR__ . "/settings.php");

require_once(ROOT . "/utils/error.php");
require_once(ROOT . "/utils/http.php");
require_once(ROOT . "/utils/arrays.php");

function getContactData(array $data) {
    $contacts = [];

    foreach ($data["entities"] as $entity) {
        if (!key_exists("vcardArray", $entity))
            continue;

        $vcardArray = $entity["vcardArray"][1];
        $fn = array_values(array_filter($vcardArray, fn(array $a) => $a[0] == "fn"))[0][3] ?? null;
        $kind = array_values(array_filter($vcardArray, fn(array $a) => $a[0] == "kind"))[0][3] ?? null;
        $email = array_map(fn(array $a) => $a[3], array_values(array_filter($vcardArray, fn(array $a) => $a[0] == "email")));
        $adr = array_values(array_filter($vcardArray, fn(array $a) => $a[0] == "adr"))[0]["label"] ?? null;
        $remarks = array_map(fn($r) => $r["title"], $entity["remarks"] ?? []);

        $contacts[] = [
            "handle" => $entity["handle"] ?? null,
            "roles" => $entity["roles"],
            "function" => $fn,
            "kind" => $kind,
            "address" => $adr,
            "email" => $email,
            "remarks" => $remarks,
        ];
    }

    return $contacts;
}

function fetchError(array $response) {
    if ($response["status"] == 404) {
        setStatusCode(404);
        $description = "The requested object does not exist.";
    } else {
        setStatusCode(500);
        $description = "Remote error: " . $response["error"];
    }

    return errorResponse("Unable to fetch results", $description);
}

function whoisIp(string $ip) {
    $response = getRequest(RDAP_URL . "/ip/" . $ip);
    if ($response["isError"]) {
        return fetchError($response);
    }

    $data = json_decode($response["body"], true);
    return [
        "query" => $ip,
        "version" => $data["ipVersion"],
        "network" => [
            "name" => $data["name"],
            "type" => $data["type"] ?? null,
            "cidr" => $data["cidr0_cidrs"][0][$data["ipVersion"] . "prefix"] . "/" . $data["cidr0_cidrs"][0]["length"],
            "startAddress" => $data["startAddress"],
            "endAddress" => $data["endAddress"],
            "status" => $data["status"],
            "remarks" => $data["remarks"]["description"][0] ?? "",
            "contacts" => getContactData($data),
        ]
    ];
}

function whoisDomain(string $domain) {
    $response = getRequest(RDAP_URL . "/domain/" . $domain);
    if ($response["isError"]) {
        return fetchError($response);
    }

    $data = json_decode($response["body"], true);

    return [
        "query" => $domain,
        "name" => $data["ldhName"],
        "unicodeName" => $data["unicodeName"] ?? $data["ldhName"],
        "nameservers" => array_map(fn($ns) => $ns["ldhName"], $data["nameservers"]),
        "registered" => array_values(
            array_filter(
                $data["events"],
                fn($e) => $e["eventAction"] == "registration"
            )
        )[0]["eventDate"] ?? null,

        "contacts" => getContactData($data),
    ];
}

return function (array $context) {
    $result = null;

    if (key_exists("ip", $_GET)) {
        $ip = $_GET["ip"];
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            setStatusCode(400);
            $result = errorResponse("Invalid IP address", "Please provide a valid IP address.");
        } else {
            $result = whoisIp($ip);
        }
    } elseif (key_exists("domain", $_GET)) {
        $domain = $_GET["domain"];

        $result = whoisDomain($domain);
    } else {
        setStatusCode(400);
        $result = errorResponse("Unknown mode", "Please specify one of the following query parameters: ip, domain");
    }

    $context["renderer"]($result);
};

