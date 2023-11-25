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

    public function countWithKeyInTimeframe(string $endpoint, string $key, string $timeframe) {
        $statement = $this->connection->prepare(<<<EOF
            SELECT COUNT(*)
            FROM `$this->table`
            WHERE
                `endpoint` = ? AND
                `key` = ? AND
                `timestamp` > ?
        EOF);
        $statement->execute([
            $endpoint,
            $key,
            (new DateTime())->sub(DateInterval::createFromDateString($timeframe))
        ]);
        return $statement->fetchColumn()[0];
    }

    public function countWithAddressInTimeframe(string $endpoint, string $address, int $timeframe) {
        $statement = $this->connection->prepare(<<<EOF
            SELECT COUNT(*)
            FROM `$this->table`
            WHERE
                `endpoint` = ? AND
                `client_address` = ? AND
                `timestamp` > CURRENT_TIMESTAMP - INTERVAL ? SECOND
        EOF);
        $statement->execute([
            $endpoint,
            $address,
            $timeframe,
        ]);
        return $statement->fetchColumn();
    }
}
