<?php

namespace App\Exports;

use Knp\Snappy\Pdf;

class NotasPdfExport
{
    public function download(array $notas): void
    {
        $schoolName  = $_ENV['SCHOOL_NAME'] ?? 'Escola';
        $generatedAt = date('d/m/Y H:i');

        ob_start();
        try {
            require __DIR__ . '/../Views/relatorios/notas_pdf.php';
            $html = ob_get_clean();
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        $snappy = new Pdf(
            $_ENV['WKHTMLTOPDF_PATH']
            ?? '/usr/local/bin/wkhtmltopdf'
        );

        $snappy->setOptions([
            'page-size'                => 'A4',
            'orientation'              => 'Portrait',
            'margin-top'               => '15mm',
            'margin-bottom'            => '15mm',
            'margin-left'              => '15mm',
            'margin-right'             => '15mm',
            'encoding'                 => 'UTF-8',
            'enable-local-file-access' => true,
        ]);

        $pdf      = $snappy->getOutputFromHtml($html);
        $filename = 'relatorio-notas-' . date('Ymd-His') . '.pdf';

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        echo $pdf;
        exit;
    }
}
