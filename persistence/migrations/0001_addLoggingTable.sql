
CREATE TABLE `ua_logs` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `timestamp` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `endpoint` VARCHAR(100),
    `endpoint_details` VARCHAR(100),
    `client_address` VARCHAR(46),
    `access_key` VARCHAR(255),
    `clean` BOOLEAN DEFAULT 0,

    INDEX (`endpoint`),
    INDEX (`client_address`),
    INDEX (`access_key`)
);