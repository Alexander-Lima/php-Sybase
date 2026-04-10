<?php

use Slim\App;
use Controller\Controllers\AcumuladorController;
use Controller\Controllers\DesController;
use Controller\Controllers\EmpresasListagemController;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function(App $app) {
    $app->get("/acumuladores", [AcumuladorController::class, 'index']);
    $app->post("/acumuladores", [AcumuladorController::class, 'compare']);

    $app->get("/des", [DesController::class, 'index']);
    $app->post("/des", [DesController::class, 'generate']);

    $app->get("/empresas", [EmpresasListagemController::class, 'index']);
    $app->get("/empresas/xlsx", [EmpresasListagemController::class, 'getXlsx']);



    // $app->get("/php/teste", function(Request $request, Response $response, array $args){
    //     $response->getBody()->write("abacate");
    // return $response->withStatus(400);

    //     return defaultJsonMessage($response, true, "abacate", 200);
    // });       
};