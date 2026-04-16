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
        </tr>
    </thead>
    <tbody>
        <?php foreach ($turmas as $turma): ?>
            <tr>
                <td><?php echo $turma['id']; ?></td>
                <td><?php echo $turma['nome']; ?></td>
                <td><?php echo $turma['ano']; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../app/Views/layout/footer.php'; ?>