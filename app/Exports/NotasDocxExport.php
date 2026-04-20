<?php

namespace App\Exports;

use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use PhpOffice\PhpWord\PhpWord;

class NotasDocxExport
{
    public function download(array $notas): void
    {
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(11);

        $section = $phpWord->addSection();

        $phpWord->addTitleStyle(1, ['bold' => true, 'size' => 18]);
        $section->addTitle($_ENV['SCHOOL_NAME'] ?? 'Escola', 1);
        $section->addText('Relatório de Notas — ' . date('d/m/Y H:i'));
        $section->addTextBreak();

        $table   = $section->addTable([
            'borderSize'  => 6,
            'borderColor' => '999999',
            'cellMargin'  => 80
        ]);
        $hFont   = ['bold' => true, 'color' => 'FFFFFF', 'size' => 10];
        $hCell   = ['bgColor' => '2C3E50'];
        $widths  = [500, 2200, 1600, 2200, 700, 900, 1200];
        $headers = [
            '#',
            'Aluno',
            'Turma',
            'Disciplina',
            'Nota',
            'Média',
            'Data',
        ];

        $table->addRow();
        foreach ($headers as $k => $label) {
            $table->addCell($widths[$k], $hCell)->addText($label, $hFont);
        }

        foreach ($notas as $i => $nota) {
            $date = $nota->data_lancamento instanceof \DateTime
                ? $nota->data_lancamento->format('d/m/Y')
                : date('d/m/Y', strtotime((string) $nota->data_lancamento));

            $table->addRow();
            $table->addCell($widths[0])->addText((string) ($i + 1));
            $table->addCell($widths[1])->addText($nota->aluno_nome);
            $table->addCell($widths[2])->addText($nota->turma_nome);
            $table->addCell($widths[3])->addText($nota->disciplina);
            $table->addCell($widths[4])->addText(number_format((float) $nota->nota, 1));
            $table->addCell($widths[5])
                ->addText(number_format((float) ($nota->media_aluno ?? 0), 2));
            $table->addCell($widths[6])->addText($date);
        }

        $filename = 'relatorio-notas-' . date('Ymd-His') . '.docx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        WordIOFactory::createWriter($phpWord, 'Word2007')->save('php://output');
        exit;
    }
}
