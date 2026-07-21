<?php

use Controller\Classes\DefaultJsonResponse;
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

    $app->group("/json/empresas", function (RouteCollectorProxy $group) {
        $group->get("/[{status}]", [EmpresasListagemController::class, 'getActivesListAsJson']);
        $group->put("", [EmpresasListagemController::class, 'updateComment']);
    });

    $app->get("/rotas", function(Request $request, Response $response, array $args) use ($app) {
        $routes = array_filter(
            $app->getRouteCollector()->getRoutes(),
            fn($route) => $route->getPattern() != '/rotas'
        );

        $routeNames = array_map(
            fn($item) => 
                sprintf("
                    <li style='padding: 5px 0; font-size: 1.1em;'>
                        <a style='color: #000000;' href='/php%s'>[%s] /php%s</a>
                    </li>",
                    $item->getPattern(),
                    strtolower(join(",", $item->getMethods())),
                    $item->getPattern()
                ),
            $routes
        );

        $response->getBody()->write(
            sprintf(
                "<h1>Rotas</h1>
                <ul style='padding-left: 4em;'>%s</ul>", 
            join('', $routeNames)));

        return $response;
    });


    // $app->get("/php/teste", function(Request $request, Response $response, array $args){
    //     $response->getBody()->write("abacate");
    // return $response->withStatus(400);

    //     return defaultJsonMessage($response, true, "abacate", 200);
    // });       
};