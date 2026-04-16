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

<ul>
    <?php foreach ($turmas as $turma): ?>
        <li><?php echo $turma['nome']; ?> - <?php echo $turma['ano']; ?></li>
    <?php endforeach; ?>
</ul>

<?php include '../app/Views/layout/footer.php'; ?>