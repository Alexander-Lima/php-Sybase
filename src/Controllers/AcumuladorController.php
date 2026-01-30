<?php
namespace Controller\Controllers;

use Controller\Service\AcumuladorService;
use Controller\Service\EmpresaService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Twig\Environment;

class AcumuladorController
{
    public function __construct(
        private ?AcumuladorService $acumuladorService,
        private ?EmpresaService $empresaService,
        private ?Environment $twig){}
    
    public function index(Request $request, Response $response, array $args) {
        $data = $this->empresaService->getEmpresas();
        $view = 
            $this->twig
                ->render("/Acumulador/index.html.twig", ["empresas" => $data]);

        $response->getBody()->write($view);

        return $response;
    }

    public function compare(Request $request, Response $response, array $args) {
        $params = $request->getParsedBody();
        $empresaBase = explode("|", $params['empresaBase'])[0];
        $empresaComparacao = explode("|", $params['empresaComparacao'])[0];

        $data = 
            $this->acumuladorService
                ->compareAcumuladores($empresaBase, $empresaComparacao);

         $view = 
            $this->twig
                ->render("/Acumulador/comparar.html.twig", ["errors" => $data]);
                
        $response->getBody()->write($view);

        return $response;
    }
}