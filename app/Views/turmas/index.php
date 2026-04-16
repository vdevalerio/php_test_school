<?php 

include '../app/Views/layout/header.php';

include '../app/Views/layout/nav.php';
include '../app/Views/layout/banner.php';

?>

<button id="criarTurmaBtn">Criar Turma</button>
<div id="criarTurmaModal" class="modal">
    <div class="modal-content">
    </div>
</div>

<ul>
    <?php foreach ($turmas as $turma): ?>
        <li><?php echo $turma['nome']; ?> - <?php echo $turma['ano']; ?></li>
    <?php endforeach; ?>
</ul>

<?php include '../app/Views/layout/footer.php'; ?>