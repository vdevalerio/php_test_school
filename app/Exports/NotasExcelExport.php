<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\IOFactory as SpreadsheetIOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class NotasExcelExport
{
    public function download(array $notas): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Notas');

        $headers = [
            '#',
            'Aluno',
            'Turma',
            'Disciplina',
            'Nota',
            'Média do Aluno',
            'Data',
        ];

        foreach ($headers as $col => $label) {
            $sheet->setCellValue(chr(65 + $col) . '1', $label);
        }

        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2C3E50'],
            ],
        ]);

        foreach ($notas as $i => $nota) {
            $row  = $i + 2;
            $date = $nota->data_lancamento instanceof \DateTime
                ? $nota->data_lancamento->format('d/m/Y')
                : date('d/m/Y', strtotime((string) $nota->data_lancamento));

            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $nota->aluno_nome);
            $sheet->setCellValue("C{$row}", $nota->turma_nome);
            $sheet->setCellValue("D{$row}", $nota->disciplina);
            $sheet->setCellValue("E{$row}", (float) $nota->nota);
            $sheet->setCellValue(
                "F{$row}",
                round((float) ($nota->media_aluno ?? 0), 2)
            );
            $sheet->setCellValue("G{$row}", $date);
        }

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'relatorio-notas-' . date('Ymd-His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        SpreadsheetIOFactory::createWriter($spreadsheet, 'Xlsx')
            ->save('php://output');
        exit;
    }
}
