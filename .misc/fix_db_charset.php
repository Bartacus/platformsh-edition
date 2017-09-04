<?php

declare(strict_types=1);

$loader = require __DIR__.'/../vendor/autoload.php';

use Platformsh\ConfigReader\Config;

$config = new Config();

if ($config->isAvailable() && isset($config->relationships['database'])) {
    foreach ($config->relationships['database'] as $endpoint) {
        if (empty($endpoint['query']['is_master'])) {
            continue;
        }

        $db = new \PDO(sprintf(
            '%s:host=%s;port=%s;dbname=%s;charset=utf8',
            $endpoint['scheme'],
            $endpoint['host'],
            $endpoint['port'],
            $endpoint['path']
        ), $endpoint['username'], $endpoint['password']);

        $db->query(sprintf(
            "ALTER DATABASE %s SET DEFAULT CHARACTER SET = 'utf8'",
            $endpoint['path']
        ));
    }
}
