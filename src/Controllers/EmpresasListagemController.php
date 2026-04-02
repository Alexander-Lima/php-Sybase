<?php
namespace Controller\Controllers;

use Controller\Service\EmpresasListagemService;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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

    public function getXlsx(Request $request, Response $response, array $args) {
        $spreadSheet = $this->service->getEmpresasXlsx();
        $filePath = __DIR__ . "/../../tmp/empresas.xlsx";
        $writer = new Xlsx($spreadSheet);
        $writer->save($filePath);

        if(!\file_exists($filePath)) {
            return $response->withStatus(404);
        }

        $response->getBody()->write(file_get_contents($filePath));
        $fileSize = filesize($filePath);
        unlink($filePath);

        return $response
                ->withAddedHeader("Content-Type", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet")
                ->withAddedHeader("Content-Disposition", "attachment;filename=empresas.xlsx")
                ->withAddedHeader("Cache-Control", "max-age=0")
                ->withAddedHeader('Content-Length', $fileSize);
    }
}