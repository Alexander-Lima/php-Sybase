<?php
namespace Controller\Controllers;

use Controller\Classes\DefaultJsonResponse;
use Controller\Classes\DefaultXlsResponse;
use Controller\Service\EmpresasListagemService;
use Controller\Service\EmpresaService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Twig\Environment;

class EmpresasListagemController
{
    public function __construct(
        private ?EmpresasListagemService $listagemService,
        private ?EmpresaService $empresaService,
        private ?Environment $twig){}
    
    public function index(Request $request, Response $response, array $args) {
        $view = 
            $this->twig->render("/Empresas/index.html.twig",
            [
                "empresas" => $this->listagemService->getEmpresas(),
                "versao" => $this->listagemService->getVersao()
            ]);

        $response->getBody()->write($view);

        return $response;
    }

    public function getListXls(Request $request, Response $response, array $args) {
        $spreadSheet = $this->listagemService->getEmpresasXlsx();
        $filePath = __DIR__ . "/../../tmp/empresas.xlsx";
 
        return DefaultXlsResponse::create($response, $filePath)
                ->saveToFile($spreadSheet)
                ->deleteAfter(true)
                ->build();
    }

    public function getECFListXls(Request $request, Response $response, array $args) {
        $spreadSheet = $this->listagemService->getEmpresasECFListXlsx($args['year']);
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
            ->withData($this->listagemService->getEmpresas(filter: $filter))
            ->isSuccessfull(true)
            ->build();
    }

    public function updateComment(Request $request, Response $response, array $args) {
        $body = $request->getParsedBody();
        $id = $body['id'] ?? null;
        $comment = $body['comment'] ?? null;

        if(!($id && $comment)) {
            return DefaultJsonResponse::create($response)
            ->withMessage("wrong parameters, supply id and message as json")
            ->withStatusCode(400)
            ->isSuccessfull(false)
            ->build();
        }

        $success = $this->empresaService->updateComment($id, $comment);

        if($success) {
            return DefaultJsonResponse::create($response)
                ->withStatusCode(200)
                ->isSuccessfull(true)
                ->build();
        }

        return DefaultJsonResponse::create($response)
            ->isSuccessfull(false)
            ->withStatusCode(400)
            ->withMessage("failed to update comment")
            ->build();
    }
}