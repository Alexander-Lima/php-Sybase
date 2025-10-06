<?php

use Slim\App;
use Controller\Controllers\AcumuladorController;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function(App $app) {
    $app->get("/php/acumuladores", [AcumuladorController::class, 'index']);

    $app->post("/php/acumuladores/comparar", [AcumuladorController::class, 'compare']);

    // $app->get("/php/teste", function(Request $request, Response $response, array $args){
    //     $response->getBody()->write(json_encode($_SERVER));

    //     return toJson($response);
    // });

    $app->redirect("/index.php", "/php/acumuladores", 200);        
    $app->redirect("/php", "/php/acumuladores", 200);        
};