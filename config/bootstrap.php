<?php
use Symfony\Component\Dotenv\Dotenv;
require_once(__DIR__ . '/../vendor/autoload.php');

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/../.env');

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
    'dbname' => $_ENV['DB_NAME'],
    'user' => $_ENV['DB_USER'],
    'password' => $_ENV['DB_PASSWORD'],
    'host' => $_ENV['DB_HOST'],
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
