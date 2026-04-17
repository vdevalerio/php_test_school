<?php 

include '../app/Views/layout/header.php';

include '../app/Views/layout/nav.php';
include '../app/Views/layout/banner.php';

?>

<?php component('modal-trigger', [
    'id'       => 'criarTurma',
    'label'    => 'Criar Turma',
    'variant'  => 'primary',
    'fetchUrl' => '/turmas/create',
]) ?>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Nome</th>
            <th>Ano</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($turmas as $turma): ?>
            <tr>
                <td><?php echo $turma['id']; ?></td>
                <td><?php echo $turma['nome']; ?></td>
                <td><?php echo $turma['ano']; ?></td>
                <td>
                    <div class="action-menu">
                        <button
                            type="button"
                            class="action-menu__toggle"
                            onclick="toggleActionMenu(this)"
                        >
                            Ações
                        </button>
                        <div class="action-menu__dropdown">
                            <a href="/turmas/<?php echo $turma['id']; ?>">Visualizar</a>
                            <?php component('modal-trigger', [
                                'id'       => 'editarTurma-' . $turma['id'],
                                'label'    => 'Editar',
                                'variant'  => 'primary',
                                'fetchUrl' => '/turmas/' . $turma['id'] . '/edit',
                            ]) ?>
                            <form method="POST" action="/turmas/<?= $turma['id'] ?>">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit">Excluir</button>
                            </form>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../app/Views/layout/footer.php'; ?>