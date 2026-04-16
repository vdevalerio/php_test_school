<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turmas</title>
</head>
<body>

    <?php include '../app/Views/layout/nav.php'; ?>
    <?php include '../app/Views/layout/banner.php'; ?>

    <ul>
        <?php foreach ($turmasFiltradas as $turma): ?>
            <li><?php echo $turma['nome']; ?> - <?php echo $turma['ano']; ?></li>
        <?php endforeach; ?>
    </ul>

</body>
</html>