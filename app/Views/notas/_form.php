<span class="close"
    onclick="this.closest('.modal').style.display='none';
    document.body.style.overflow=''">&times;
</span>

<?php

use App\Models\Aluno;
use App\Models\Turma; ?>

<form action="<?= $action ?>" method="POST">
    <input type="hidden" name="_method" value="<?= $method ?>">

    <label for="aluno_id">Aluno</label>
    <select id="aluno_id" name="aluno_id">
        <?php foreach (Aluno::all() as $alunoItem): ?>
            <option value="<?= $alunoItem['id'] ?>" <?= $alunoItem['id'] == ($nota->aluno_id ?? null) ? 'selected' : '' ?>>
                <?= htmlspecialchars($alunoItem['nome']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="disciplina">Disciplina</label>
    <input type="text" id="disciplina" name="disciplina"
           value="<?= htmlspecialchars($nota->disciplina ?? '') ?>" required>

    <label for="nota">Nota</label>
    <input type="number" id="nota" name="nota"
           value="<?= htmlspecialchars($nota->nota ?? '') ?>" required>

    <button type="submit"><?= $submitLabel ?></button>
</form>
