<?php

use Controller\Config\Database;
use Controller\Repository\AcumuladorRepository;
use Controller\Repository\AcumuladorRepositoryInterface;
use Controller\Service\AcumuladorService;
use Controller\Controllers\AcumuladorController;
use Controller\Repository\EmpresaRepository;
use Controller\Repository\EmpresaRepositoryInterface;
use Controller\Controllers\DesController;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use function DI\autowire;

return [
    AcumuladorRepositoryInterface::class => autowire(AcumuladorRepository::class),
    AcumuladorService::class => autowire(),
    AcumuladorController::class => autowire(),
    DesController::class => autowire(),
    Database::class => autowire(),
    Environment::class => fn()  => 
        new Environment(
            new FilesystemLoader(__DIR__ . "/../src/Views"), 
            ['cache' => __DIR__ . "/../src/Views/cache"]),
            // ['cache' => false]),
    EmpresaRepositoryInterface::class => autowire(EmpresaRepository::class),
];
