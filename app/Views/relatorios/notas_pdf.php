<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #222;
        }

        .report-header {
            text-align: center;
            margin-bottom: 24px;
            padding-bottom: 12px;
            border-bottom: 2px solid #333;
        }
        .report-header h1 { font-size: 20px; }
        .report-header p  { font-size: 11px; color: #555; margin-top: 4px; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        th {
            background: #2c3e50;
            color: #fff;
            padding: 6px 8px;
            text-align: left;
            font-size: 11px;
        }
        td {
            padding: 5px 8px;
            border-bottom: 1px solid #ddd;
            vertical-align: top;
        }
        tr:nth-child(even) td { background: #f7f7f7; }

        .media-cell       { font-weight: bold; color: #27ae60; }
        .media-cell.mid   { color: #e67e22; }
        .media-cell.low   { color: #e74c3c; }

        .report-footer {
            margin-top: 20px;
            font-size: 10px;
            color: #888;
            text-align: right;
        }
    </style>
</head>
<body>

    <div class="report-header">
        <h1><?= htmlspecialchars($schoolName) ?></h1>
        <p>Relatório de Notas &mdash; Gerado em <?= $generatedAt ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Aluno</th>
                <th>Turma</th>
                <th>Disciplina</th>
                <th>Nota</th>
                <th>Média do Aluno</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($notas as $i => $nota):
            $media      = round((float) ($nota->media_aluno ?? 0), 2);
            $mediaClass = $media >= 7 ? 'media-cell'
                        : ($media >= 5 ? 'media-cell mid' : 'media-cell low');
        ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($nota->aluno_nome) ?></td>
                <td><?= htmlspecialchars($nota->turma_nome) ?></td>
                <td><?= htmlspecialchars($nota->disciplina) ?></td>
                <td><?= number_format((float) $nota->nota, 1) ?></td>
                <td class="<?= $mediaClass ?>"><?= number_format($media, 2) ?></td>
                <td><?= $nota->data_lancamento instanceof \DateTime
                        ? $nota->data_lancamento->format('d/m/Y')
                        : date('d/m/Y', strtotime((string) $nota->data_lancamento)) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="report-footer">
        Total de registros: <?= count($notas) ?>
    </div>

</body>
</html>
