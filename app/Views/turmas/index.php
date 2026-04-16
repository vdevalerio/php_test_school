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
            <th>Visualizar</th>
            <th>Editar</th>
            <th>Excluir</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($turmas as $turma): ?>
            <tr>
                <td><?php echo $turma['id']; ?></td>
                <td><?php echo $turma['nome']; ?></td>
                <td><?php echo $turma['ano']; ?></td>
                <td>
                    <button 
                        type="button" 
                        onclick="window.location.href='/turmas/<?php echo $turma['id']; ?>'"
                    >
                        Visualizar
                    </button>
                </td>
                <td>
                    <?php component('modal-trigger', [
                        'id'       => 'editarTurma-' . $turma['id'],
                        'label'    => 'Editar',
                        'variant'  => 'primary',
                        'fetchUrl' => '/turmas/' . $turma['id'] . '/edit',
                    ]) ?>
                </td>
                <td>
                    <form method="POST" action="/turmas/<?= $turma['id'] ?>">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit">Excluir</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../app/Views/layout/footer.php'; ?>