<?php 

include '../app/Views/layout/header.php';

include '../app/Views/layout/nav.php';
include '../app/Views/layout/banner.php';

?>

<ul>
    <?php foreach ($turmas as $turma): ?>
        <li><?php echo $turma['nome']; ?> - <?php echo $turma['ano']; ?></li>
    <?php endforeach; ?>
</ul>

<?php include '../app/Views/layout/footer.php'; ?>