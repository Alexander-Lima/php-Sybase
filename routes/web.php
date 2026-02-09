<?php

use Slim\App;
use Controller\Controllers\AcumuladorController;
use Controller\Controllers\DesController;
use Controller\Controllers\EmpresasListagemController;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function(App $app) {
    $app->get("/php/acumuladores", [AcumuladorController::class, 'index']);
    $app->post("/php/acumuladores/comparar", [AcumuladorController::class, 'compare']);

    $app->get("/php/des", [DesController::class, 'index']);
    $app->post("/php/des", [DesController::class, 'generate']);

    $app->get("/php/empresas", [EmpresasListagemController::class, 'index']);

    // $app->get("/php/teste", function(Request $request, Response $response, array $args){
    //     // $cn = explode(",", $_SERVER['SSL_CLIENT_SUBJECT']);
    //     // $name = explode(":", $cn[0]);
    //     // $razao = str_replace("CN=", "", $name[0]);
    //     // $cnpj = $name[1];

    //     // if($cnpj === "10534874000182") {
    //     //     $response->getBody()->write("NOME: {$razao} <br> CNPJ: {$name[1]}");
    //     //     return $response;
    //     // }

    //     $response->getBody()->write("abacate");
    //     // return $response->withStatus(400);

    //     return defaultJsonMessage($response, true, "abacate", 200);
    // });       
};