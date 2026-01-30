<?php

namespace Controller\Classes;

use Slim\Psr7\Request;

class DesGenerator
{
    public function __construct(
        private ?Request $request, 
        private string $folderName){}

    private function getXmlData(): array {
        $files = array_diff(scandir($this->folderName), [".", ".."]);
        sort($files);
            
        $data = [];

        foreach ($files as $file) {
            $xml = simplexml_load_file("{$this->folderName}/{$file}");
            $invoiceData = new \stdClass();

            if(!isset($xml->infNFSe)) {
                continue;
            } 

            $invoiceData->dCompet = (string) $xml->infNFSe->DPS->infDPS->dCompet ?? "";
            $invoiceData->nNFSE = (string) $xml->infNFSe->nNFSe ?? "";
            $invoiceData->vBC = (string) $xml->infNFSe->valores->vBC ?? "";
            $invoiceData->CPF = (string) $xml->infNFSe->DPS->infDPS->toma->CPF ?? "";
            $invoiceData->CNPJ = (string) $xml->infNFSe->DPS->infDPS->toma->CNPJ ?? "";

            $data[] = $invoiceData;
        }

        return $data;
    }

    private function createIssFile(array $data): void {
        $params = $this->request->getParsedBody();
        $companyName = $params['razaoSocial'];
        $cityRegister = $params['inscricaoMunicipal'];

        $header = "H|" . date("d/m/Y") . "||" . $_ENV["DES_VERSION"] . "|{$cityRegister}|||{$companyName}||||0|2|1|\n";
        $filename = "{$this->folderName}/DES.iss";

        $file = fopen($filename, 'a');
        fwrite($file, $header);

        foreach ($data as $item) {
            fwrite($file, $this->generateBlockE($item));
        }

        fclose($file);
    }

    private function generateBlockE(\stdClass $data): string {
        $params = $this->request->getParsedBody();
        $serviceCode = $params['codigoServico'];
        $subItem = $params['subItem'];
        $taxRegime = $params['regimeTributacao'];
        $date = DesGenerator::formatDate($data->dCompet);
        $nfse = substr($date, 4, 4) . $data->nNFSE;
        $cityCode = "3106200";
        $countryCode = "1058";

        return \sprintf(
            "E|%s|%s|%s|5|0||1|1|%s|%s|2|%s|%s|%s|%s|0.00|1|2||%s|%s|DIVERSOS|||||||||||||||||||||||%s|%s|||||||\n",
            $date,
            $serviceCode,
            $subItem,
            $cityCode,
            $taxRegime,
            $nfse,
            $nfse,
            $data->vBC,
            $data->vBC,
            $data->CNPJ,
            $data->CPF,
            $cityCode,
            $countryCode
        );
    }

    private function formatDate(string $stringDate): string {
        $date = explode("-", $stringDate);
        return "{$date[2]}{$date[1]}{$date[0]}";
    }

    public function clear(): void {
        $files = array_diff(scandir($this->folderName), [".", ".."]);

        if(!$files) {
            return;
        }
            
        foreach($files as $file) {
            unlink("{$this->folderName}/{$file}");
        }

        rmdir($this->folderName);
    }

    public function generateDes(): void {
        $data = $this->getXmlData();
        $this->createIssFile($data);
    }
}