<?php

class LogEntry {
    public int $id;
    public DateTime $timestamp;
    public string $endpoint;
    public string $endpointDetails;
    public string $clientAddress;
    public string $accessKey;
    public bool $isClean;

    public function __construct(
        string $endpoint,
        string $endpointDetails,
        string $clientAddress,
        string $accessKey,
    ) {
        $this->endpoint = $endpoint;
        $this->endpointDetails = $endpointDetails;
        $this->clientAddress = $clientAddress;
        $this->accessKey = $accessKey;
    }
}