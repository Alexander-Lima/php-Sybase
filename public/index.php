<?php

use Dotenv\Dotenv;
use Slim\Factory\AppFactory;
use DI\ContainerBuilder;

require_once '../vendor/autoload.php';

$dotnev = Dotenv::createImmutable(__DIR__ . "/../");
$dotnev->load();

$builder = new ContainerBuilder();
$builder->addDefinitions(__DIR__ . "/../config/di.php");
$container = $builder->build();

AppFactory::setContainer($container);
$app = AppFactory::create();
(require '../routes/web.php')($app);

$app->run();

