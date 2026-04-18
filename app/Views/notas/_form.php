<?php

use App\Models\Aluno;

$alunos = Aluno::all();
$date   = isset($nota->data_lancamento)
    ? $nota->data_lancamento->format('Y-m-d')
    : date('Y-m-d');
?>

<span class="close" data-close-modal>&times;</span>

<form action="<?= $action ?>" method="POST">
    <input type="hidden" name="_method" value="<?= $method ?>">

    <label for="aluno_id">Aluno</label>
    <select id="aluno_id" name="aluno_id">
        <?php foreach ($alunos as $alunoItem): ?>
            <?php
            $selected = $alunoItem['id'] == ($nota->aluno_id ?? null)
                ? 'selected'
                : '';
            ?>
            <option value="<?= $alunoItem['id'] ?>" <?= $selected ?>>
                <?= htmlspecialchars($alunoItem['nome']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="disciplina">Disciplina</label>
    <input
        type="text"
        id="disciplina"
        name="disciplina"
        value="<?= htmlspecialchars($nota->disciplina ?? '') ?>"
        required
    >

    <label for="nota">Nota</label>
    <input
        type="number"
        id="nota"
        name="nota"
        value="<?= htmlspecialchars($nota->nota ?? '') ?>"
        required
    >

    <label for="data_lancamento">Data de Lançamento</label>
    <input
        type="date"
        id="data_lancamento"
        name="data_lancamento"
        value="<?= $date ?>"
    >

    <button type="submit"><?= $submitLabel ?></button>
</form>
