<?php

function request(string $method, string $url, string $body = null, array $headers = []) {
    $curlResource = curl_init();

    curl_setopt($curlResource, CURLOPT_URL, $url);
    curl_setopt($curlResource, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curlResource, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curlResource, CURLOPT_FOLLOWLOCATION, true);

    if ($body)
        curl_setopt($curlResource, CURLOPT_POSTFIELDS, $body);

    if ($headers) {
        if (!array_is_list($headers))
            $headers = array_map(fn($k, $v) => $k . ": " . $v, array_keys($headers), array_values($headers));


        curl_setopt($curlResource, CURLOPT_HTTPHEADER, $headers);
    }

    $result = curl_exec($curlResource);

    curl_close($curlResource);

    return $result;
}

function getRequest(string $url, array $headers = []) {
    return request("GET", $url, null, $headers);
}

function postRequest(string $url, string $body = null, $headers = []) {
    return request("POST", $url, $body, $headers);
}