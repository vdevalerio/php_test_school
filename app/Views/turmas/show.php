<?php 

include '../app/Views/layout/header.php';

include '../app/Views/layout/nav.php';
?>

<h1>#<?= $turma['id'] ?> - <?= $turma['nome'] ?> - <?= $turma['ano'] ?></h1>

<?php include '../app/Views/layout/footer.php'; ?>