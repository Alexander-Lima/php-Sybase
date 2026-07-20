<?php
use Slim\Factory\AppFactory;
use DI\ContainerBuilder;

$builder = new ContainerBuilder();
$builder->addDefinitions(__DIR__ . "/../config/di.php");
$container = $builder->build();

AppFactory::setContainer($container);
$app = AppFactory::create();
$app->setBasePath("/php");
$app->addBodyParsingMiddleware();

(require_once '../routes/web.php')($app);

$app->run();