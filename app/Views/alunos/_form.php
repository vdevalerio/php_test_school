<span class="close"
    onclick="this.closest('.modal').style.display='none';
    document.body.style.overflow=''">&times;
</span>

<?php use App\Models\Turma; ?>

<form action="<?= $action ?>" method="POST">
    <input type="hidden" name="_method" value="<?= $method ?>">

    <label for="nome">Nome</label>
    <input type="text" id="nome" name="nome"
           value="<?= htmlspecialchars($aluno->nome ?? '') ?>" required>

    <label for="email">E-mail</label>
    <input type="email" id="email" name="email"
           value="<?= htmlspecialchars($aluno->email ?? '') ?>" required>

    <label for="turma_id">Turma</label>
    <select id="turma_id" name="turma_id">
        <?php foreach (Turma::all() as $turmaItem): ?>
            <option value="<?= $turmaItem['id'] ?>" <?= $turmaItem['id'] == ($aluno->turma_id ?? null) ? 'selected' : '' ?>>
                <?= htmlspecialchars($turmaItem['nome']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit"><?= $submitLabel ?></button>
</form>
