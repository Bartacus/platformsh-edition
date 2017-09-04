<?php

declare(strict_types=1);

use Platformsh\ConfigReader\Config;

$config = new Config();

if ($config->isAvailable()) {
    if (isset($config->project_entropy)) {
        $container->setParameter('secret', $config->project_entropy);
    }

    $container->setParameter('mailer_transport', 'mail');
}
