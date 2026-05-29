<?php
namespace Controller\Service;

use Controller\Repository\EmpresaListagemRepository;
use Controller\Repository\SistemaRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmpresasListagemService
{
    public function __construct(
        private EmpresaListagemRepository $empresasRepository,
        private SistemaRepository $sistemaRepository){}

    public function getEmpresas(): array {
        return $this->empresasRepository->getListaEmpresas();
    }

    public function getVersao(): string {
        return $this->sistemaRepository->getVersion();
    }

    public function getEmpresasXlsx(): Spreadsheet {
        return $this
            ->getSpreadsheetWithData(
                "Listagem empresas ECF",
                $this->empresasRepository->getListaEmpresas()
            );
    }

    public function getEmpresasECFListXlsx(string $year): Spreadsheet | null {
        if(!is_numeric($year)) {
            return null;
        }

        return $this
            ->getSpreadsheetWithData(
                "Listagem empresas ECF",
                $this->empresasRepository->getListaEmpresasECF($year)
            );
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

    private function getSpreadsheetWithData(string $title, array $data): Spreadsheet {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($title);
        $spreadsheet->getActiveSheet()->fromArray($data, "-", "A2");

        $this->setHeaders(array_keys($data[0]), $sheet);
        $this->resizeAllColumns($sheet);

        return $spreadsheet;
    }
}