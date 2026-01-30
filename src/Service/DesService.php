<?php
namespace Controller\Service;

use Controller\Classes\DesGenerator;
use Controller\Classes\DefaultJsonResponse;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use ZipArchive;

class DesService
{
    public function __construct(private Request $request, private Response $response){}

    public function processDes(): Response {
        $uploadedFiles = $this->request->getUploadedFiles();
        $zipFile = $uploadedFiles['files'];
    
        if (empty($zipFile)) {
            return DefaultJsonResponse::create($this->response)
                ->withMessage("Nenhum arquivo enviado.")
                ->build();
        }

        if ($zipFile->getError() !== UPLOAD_ERR_OK) {
            return DefaultJsonResponse::create($this->response)
                ->withMessage("Falha no Upload.")
                ->build();
        }
        
        $zip = new ZipArchive();
        $folderName = "{$_ENV["UPLOAD_FILEPATH"]}/" . uniqid("process", true);
        $finalFilename = "{$folderName}/DES.iss";
        
        if(!($zip->open($zipFile->getFilePath()) && mkdir($folderName))) {
            return DefaultJsonResponse::create($this->response)
                ->withMessage("Falha ao extrair arquivos.")
                ->build();
        } 

        $zip->extractTo($folderName);
        $desGenerator = new DesGenerator($this->request, $folderName);
        $desGenerator->generateDes();

        $response = sendFile($this->response, $finalFilename);
        $desGenerator->clear();

        return $response;
    }
}