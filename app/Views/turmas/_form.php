<span class="close" data-close-modal>&times;</span>

<form action="<?= $action ?>" method="POST">
    <input type="hidden" name="_method" value="<?= $method ?>">

    <label for="nome">Nome</label>
    <input
        type="text"
        id="nome"
        name="nome"
        value="<?= htmlspecialchars($turma->nome ?? '') ?>"
        required
    >

    <label for="ano">Ano</label>
    <input
        type="number"
        id="ano"
        name="ano"
        value="<?= htmlspecialchars($turma->ano ?? '') ?>"
        required
    >

    <button type="submit"><?= $submitLabel ?></button>
</form>