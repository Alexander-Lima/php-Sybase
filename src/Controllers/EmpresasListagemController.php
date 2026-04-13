<?php
namespace Controller\Controllers;

use Controller\Classes\DefaultJsonResponse;
use Controller\Classes\DefaultXlsResponse;
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

    public function getListXls(Request $request, Response $response, array $args) {
        $spreadSheet = $this->service->getEmpresasXlsx();
        $filePath = __DIR__ . "/../../tmp/empresas.xlsx";
 
        return DefaultXlsResponse::create($response, $filePath)
                ->saveToFile($spreadSheet)
                ->deleteAfter(true)
                ->build();
    }

    public function getECFListXls(Request $request, Response $response, array $args) {
        $spreadSheet = $this->service->getEmpresasECFListXlsx($args['year']);
        $filePath = __DIR__ . "/../../tmp/empresas_ecf.xlsx";

        return DefaultXlsResponse::create($response, $filePath)
                ->saveToFile($spreadSheet)
                ->deleteAfter(true)
                ->build();
    }
}