<?php
use Slim\Factory\AppFactory;
use DI\ContainerBuilder;
use Controller\Middleware\ErrorMiddleware;

$builder = new ContainerBuilder();
$builder->addDefinitions(__DIR__ . "/../config/di.php");
$container = $builder->build();

AppFactory::setContainer($container);
$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->setBasePath("/php");
$app
    ->addErrorMiddleware(true, true, true)
    ->setDefaultErrorHandler(new ErrorMiddleware($app->getResponseFactory()));

(require_once '../routes/web.php')($app);

$app->run();