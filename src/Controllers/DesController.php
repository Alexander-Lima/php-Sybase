<?php
namespace Controller\Controllers;

use Controller\Classes\DefaultJsonResponse;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Controller\Service\DesService;
use Twig\Environment;

class DesController
{
    public function __construct(private ?Environment $twig){}
    
    public function index(Request $request, Response $response) {
        $view = $this->twig->render("/DES/index.html.twig");

        $response->getBody()->write($view);

        return $response;
    }

    public function generate(Request $request, Response $response) {
        return (new DesService($request, $response))->processDes();
    }
}