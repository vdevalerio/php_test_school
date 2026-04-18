<?php

use App\Models\Turma;

$turmas = Turma::all();
?>

<span class="close" data-close-modal>&times;</span>

<form action="<?= $action ?>" method="POST">
    <input type="hidden" name="_method" value="<?= $method ?>">

    <label for="nome">Nome</label>
    <input
        type="text"
        id="nome"
        name="nome"
        value="<?= htmlspecialchars($aluno->nome ?? '') ?>"
        required
    >

    <label for="email">E-mail</label>
    <input
        type="email"
        id="email"
        name="email"
        value="<?= htmlspecialchars($aluno->email ?? '') ?>"
        required
    >

    <label for="turma_id">Turma</label>
    <select id="turma_id" name="turma_id">
        <?php foreach ($turmas as $turmaItem): ?>
            <?php
            $selected = $turmaItem['id'] == ($aluno->turma_id ?? null)
                ? 'selected'
                : '';
            ?>
            <option value="<?= $turmaItem['id'] ?>" <?= $selected ?>>
                <?= htmlspecialchars($turmaItem['nome']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit"><?= $submitLabel ?></button>
</form>
