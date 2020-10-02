<?php

use Symfony\Component\Console\Application;

require __DIR__ . '/../vendor/autoload.php';

$em = require_once __DIR__ . '/../config/bootstrap.php';

$container = new DI\Container();

$container->set(\Doctrine\ORM\EntityManager::class, $em);

$application = new Application();
$application->add($container->get(\App\Command\ImportConversationCommand::class));
$application->add($container->get(\App\Command\CreateStatisticsCommand::class));
$application->run();
