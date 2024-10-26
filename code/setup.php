<?php

declare(strict_types=1);

use ITB\ShopwareRemoteApiTestRunner\ApiTest\AdminApiRunner;
use ITB\ShopwareRemoteApiTestRunner\Setup\AfterInstallConfigurator;
use ITB\ShopwareRemoteApiTestRunner\Setup\DatabaseBackupExecutor;
use Symfony\Component\Dotenv\Dotenv;

// Get command-line arguments
$locale = $argv[1] ?? null;
$currencyIsoCode = $argv[2] ?? null;

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__ . '/.env');

require __DIR__ . '/vendor/shopware/platform/src/Core/TestBootstrap.php';

$remotelyCalledApiTest = new AdminApiRunner();
$remotelyCalledApiTest->beforeCall(true);
$remotelyCalledApiTest->afterCall(true);

$afterInstallConfigurator = new AfterInstallConfigurator();
if ($locale !== null) {
    $afterInstallConfigurator->changeSystemLanguage($locale);
}
if ($currencyIsoCode !== null) {
    $afterInstallConfigurator->changeSystemCurrency($currencyIsoCode);
}

$databaseBackupExecutor = new DatabaseBackupExecutor(__DIR__ . '/var/dump');
$databaseBackupExecutor->createDatabaseBackup();
