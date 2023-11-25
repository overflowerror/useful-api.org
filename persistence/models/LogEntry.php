<?php

class LogEntry {
    public int $id;
    public DateTime $timestamp;
    public string $endpoint;
    public string $clientAddress;
    public string $accessKey;
    public bool $isClean;

    public function __construct(
        string $endpoint,
        string $clientAddress,
        string $accessKey,
    ) {
        $this->endpoint = $endpoint;
        $this->clientAddress = $clientAddress;
        $this->accessKey = $accessKey;
    }
}