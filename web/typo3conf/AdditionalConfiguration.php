<?php

declare(strict_types = 1);

use Bartacus\Bundle\BartacusBundle\Config\ConfigLoader;
use Platformsh\ConfigReader\Config;
use Symfony\Component\HttpKernel\Kernel;
use TYPO3\CMS\Core\Cache\Backend\RedisBackend;
use TYPO3\CMS\Core\Utility\GeneralUtility;

$config = new Config();
if ($config->isAvailable()) {
    if (isset($config->relationships['database'])) {
        foreach ($config->relationships['database'] as $endpoint) {
            if (empty($endpoint['query']['is_master'])) {
                continue;
            }

            $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['driver'] = 'mysqli';
            $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['host'] = $endpoint['host'];
            $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['port'] = $endpoint['port'];
            $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['dbname'] = $endpoint['path'];
            $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['user'] = $endpoint['username'];
            $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['password'] = $endpoint['password'];
            $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['charset'] = 'utf8';

            $GLOBALS['TYPO3_CONF_VARS']['MAIL'] = [
                'transport_sendmail_command' => '/usr/sbin/sendmail -t -i ',
            ];
        }
    }

    if (isset($config->relationships['redis'])) {
        $redisHost = null;
        $redisPort = null;

        foreach ($config->relationships['redis'] as $endpoint) {
            $redisHost = $endpoint['host'];
            $redisPort = $endpoint['port'];
        }

        if ($redisHost && $redisPort) {
            $list = [
                'cache_pages' => 86400,
                'cache_pagesection' => 86400,
                'cache_hash' => 86400,
                'extbase_object' => 0,
                'extbase_reflection' => 0,
                'extbase_datamapfactory_datamap' => 0,
            ];

            $counter = 3;
            foreach ($list as $key => $lifetime) {
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$key]['backend'] = RedisBackend::class;
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$key]['options'] = [
                    'database' => $counter++,
                    'hostname' => $redisHost,
                    'port' => $redisPort,
                    'defaultLifetime' => $lifetime,
                ];
            }
        }
    }
}

if (GeneralUtility::getApplicationContext()->isDevelopment()) {
    $GLOBALS['TYPO3_CONF_VARS']['BE']['debug'] = true;
    $GLOBALS['TYPO3_CONF_VARS']['BE']['sessionTimeout'] = 31536000; // One year!
    $GLOBALS['TYPO3_CONF_VARS']['FE']['debug'] = true;
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['sqlDebug'] = 1;
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['displayErrors'] = 1;
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['systemLogLevel'] = 1;
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['enableDeprecationLog'] = 1;
}

if (\extension_loaded('zlib')) {
    $GLOBALS['TYPO3_CONF_VARS']['BE']['compressionLevel'] = 9;
    $GLOBALS['TYPO3_CONF_VARS']['FE']['compressionLevel'] = 9;
}

$kernel = $GLOBALS['kernel'];
if ($kernel instanceof Kernel) {
    $configLoader = $kernel->getContainer()->get(ConfigLoader::class);
    $configLoader->loadFromAdditionalConfiguration();
}
