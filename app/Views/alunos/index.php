<?php 

include '../app/Views/layout/header.php';

include '../app/Views/layout/nav.php';
include '../app/Views/layout/banner.php';

?>

<?php component('modal-trigger', [
    'id'       => 'criarAluno',
    'label'    => 'Criar Aluno',
    'variant'  => 'primary',
    'fetchUrl' => '/alunos/create',
]) ?>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Turma</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($alunos as $aluno): ?>
            <tr>
                <td><?php echo $aluno['id']; ?></td>
                <td><?php echo $aluno['nome']; ?></td>
                <td><?php echo $aluno['email']; ?></td>
                <td><?php echo $aluno['turma_id']; ?></td>
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
                            <a href="/alunos/<?php echo $aluno['id']; ?>">Visualizar</a>
                            <?php component('modal-trigger', [
                                'id'       => 'editarAluno-' . $aluno['id'],
                                'label'    => 'Editar',
                                'variant'  => 'primary',
                                'fetchUrl' => '/alunos/' . $aluno['id'] . '/edit',
                            ]) ?>
                            <form method="POST" action="/alunos/<?= $aluno['id'] ?>">
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