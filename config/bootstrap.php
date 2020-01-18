<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$entitiesPath = [
    __DIR__ . '/../src/Entity'
];

$isDevMode = false;
$proxyDir = null;
$cache = null;
$useSimpleAnnotationReader = false;

$connectionParams = array(
    'dbname' => 'postgres',
    'user' => 'postgres',
    'password' => 'postgres',
    'host' => 'localhost',
    'driver' => 'pdo_pgsql',
);

$config = Setup::createAnnotationMetadataConfiguration(
    $entitiesPath,
    $isDevMode,
    $proxyDir,
    $cache,
    $useSimpleAnnotationReader
);
$entityManager = EntityManager::create($connectionParams, $config);

return $entityManager;
