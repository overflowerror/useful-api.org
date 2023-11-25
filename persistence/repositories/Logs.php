<?php

require_once(__DIR__ . "/../models/LogEntry.php");

class Logs {
    private string $table = "ua_logs";

    private PDO $connection;

    public function __construct(PDO $connection) {
        $this->connection = $connection;
    }

    public function add(LogEntry $entry) {
        $statement = $this->connection->prepare(<<<EOF
            INSERT INTO `$this->table`
                (`endpoint`, `client_address`, `access_key`) VALUES
                (?, ?, ?)
        EOF);
        $statement->execute([
            $entry->endpoint,
            $entry->clientAddress,
            $entry->accessKey,
        ]);
    }
}
