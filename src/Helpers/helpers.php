<?php

use Controller\Classes\DefaultJsonResponse;
use Psr\Http\Message\ResponseInterface as Response;

function sendFile(Response $response, string $filename) {
    $file = file_get_contents($filename);

    if(!$file) {
        return DefaultJsonResponse::create($response)
            ->withMessage("Falha no envio do arquivo.")
            ->build();
    }
    
    $response->getBody()->write($file);

    return $response
        ->withAddedHeader("Content-Description", "File Transfer")
        ->withAddedHeader("Content-Type", "application/octet-stream")
        ->withAddedHeader("Content-Disposition", "attachment; filename='DES.iss'")
        ->withAddedHeader("Content-Length", filesize($filename))
        ->withStatus(200);
}


