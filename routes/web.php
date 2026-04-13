<?php

use Slim\App;
use Controller\Controllers\AcumuladorController;
use Controller\Controllers\DesController;
use Controller\Controllers\EmpresasListagemController;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteCollectorProxy;

return function(App $app) {
    $app->group("/acumuladores", function (RouteCollectorProxy $group) {
        $group->get("", [AcumuladorController::class, 'index']);
        $group->post("", [AcumuladorController::class, 'compare']);
    });

    $app->group("/des", function (RouteCollectorProxy $group) {
        $group->get("", [DesController::class, 'index']);
        $group->post("", [DesController::class, 'generate']);
    });

    $app->group("/empresas", function (RouteCollectorProxy $group) {
        $group->get("", [EmpresasListagemController::class, 'index']);
        $group->get("/listagem_xls", [EmpresasListagemController::class, 'getListXls']);
        $group->get("/ecf_xls/{year}", [EmpresasListagemController::class, 'getECFListXls']);
    });

    // $app->get("/php/teste", function(Request $request, Response $response, array $args){
    //     $response->getBody()->write("abacate");
    // return $response->withStatus(400);

    //     return defaultJsonMessage($response, true, "abacate", 200);
    // });       
};