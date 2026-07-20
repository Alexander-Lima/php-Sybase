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
        $view = 
            $this->twig->render("/Empresas/index.html.twig",
            [
                "empresas" => $this->service->getEmpresas(),
                "versao" => $this->service->getVersao()
            ]);

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

    public function getActivesListAsJson(Request $request, Response $response, array $args) {
        $filter = null;

        if(isset($args["status"])) {
            $par = strtoupper($args["status"]);

            $filter = function($objKey) use ($par){
                if($par == "ATIVA") {
                    return $objKey['STATUS DOMÍNIO'] == $par || $objKey['STATUS DOMÍNIO'] == "ATIVA-SEM MOV.";
                }
                
                return $objKey['STATUS DOMÍNIO'] == $par;
            };
        }

        return DefaultJsonResponse::create($response)
            ->withData($this->service->getEmpresas(filter: $filter))
            ->isSuccessfull(true)
            ->build();
    }

    public function updateComment(Request $request, Response $response, array $args) {
        $body = $request->getParsedBody();
        return DefaultJsonResponse::create($response)
            ->withData($this->service->getEmpresas())
            ->isSuccessfull(true)
            ->build();
    }
}