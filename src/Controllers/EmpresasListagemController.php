<?php
namespace Controller\Controllers;

use Controller\Service\EmpresasListagemService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Twig\Environment;

class EmpresasListagemController
{
    public function __construct(
        private ?EmpresasListagemService $service,
        private ?Environment $twig){}
    
    public function index(Request $request, Response $response, array $args) {
        $data = $this->service->getEmpresas();
        $view = $this->twig->render("/Empresas/index.html.twig", ["empresas" => $data]);

        $response->getBody()->write($view);

        return $response;
    }
}