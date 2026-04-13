<?php

namespace Controller\Classes;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Psr\Http\Message\ResponseInterface as Response;

class DefaultXlsResponse
{
    private bool $deleteAfter = false;
    private function __construct(private Response $response, private string $filePath){}

    public static function create(Response $response, string $filePath): DefaultXlsResponse {
        return new DefaultXlsResponse($response, $filePath);
    }

    public function saveToFile(?Spreadsheet $spreadsheet): DefaultXlsResponse {
        if($spreadsheet) {
            (new Xlsx($spreadsheet))->save($this->filePath);
        }

        return $this;
    }

    public function deleteAfter(bool $delete): DefaultXlsResponse {
        $this->deleteAfter = $delete;

        return $this;
    }

    public function build(): Response {
        if(!\file_exists($this->filePath)) {
            return $this->response->withStatus(500);
        }
        
        $filesize = filesize($this->filePath);

        $this->response
            ->getBody()
            ->write(file_get_contents($this->filePath));

        if($this->deleteAfter) {
            unlink($this->filePath);
        }

        return $this->response
                ->withAddedHeader("Content-Type", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet")
                ->withAddedHeader("Content-Disposition", "attachment;filename=" . basename($this->filePath))
                ->withAddedHeader("Cache-Control", "max-age=0")
                ->withAddedHeader('Content-Length', $filesize);
    }
}