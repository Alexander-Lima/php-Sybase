<?php
namespace Controller\Service;

use Controller\Repository\EmpresaListagemRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmpresasListagemService
{
    public function __construct(private EmpresaListagemRepository $repository){}

    public function getEmpresas(): array {
        return $this->repository->getListaEmpresas();
    }

    public function getEmpresasXlsx(): Spreadsheet {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Listagem empresas");
        
        $headers = [
            "CNPJ",
            "RAZÃO SOCIAL",
            "UF",
            "INSCRIÇÃO ESTADUAL",
            "MUNICÍPIO",
            "INSCRIÇÃO MUNICIPAL",
            "REGIME TRIBUTÁRIO",
            "STATUS DOMÍNIO"
        ];

        $this->setHeaders($headers, $sheet);
        $data = $this->formatData($this->repository->getListaEmpresas());
        $sheet->fromArray($data, NULL, "A2");
        $this->resizeAllColumns($sheet);

        return $spreadsheet;
    }

    private function formatData(array $data): array {
        foreach($data as &$item) {
            if($item['status_dominio'] === 'INATIVA') {
                $item['status_dominio'] = "INATIVA |{$item['tipo_inatividade']}| {$item['data_inatividade']}";
                $item['tipo_inatividade'] = $item['data_inatividade']  = NULL;
            }
        }

        return $data;
    }

    private function setHeaders(array $headers, Worksheet $sheet): void {
        $startColumn = "A";
        $styleArray = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => Color::COLOR_DARKBLUE, 
                ],
            ],
            'font' => [
                'bold' => true,
                'color' => ['rgb' => Color::COLOR_WHITE]
            ]
        ];


        for($index = 0; $index < \count($headers); $index++) {
            $sheet->getStyle("{$startColumn}1")->applyFromArray($styleArray);
            $sheet->setCellValue("{$startColumn}1", $headers[$index]);
            ++$startColumn;
        }
    }

    private function resizeAllColumns(Worksheet $sheet) {
        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }
    }
}