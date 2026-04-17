<?php

use App\Models\Turma;

include '../app/Views/layout/header.php';

include '../app/Views/layout/nav.php';
?>

<h1>#<?= $aluno->id ?> - <?= $aluno->nome ?> - <?= $aluno->turma()->nome ?></h1>
<h4><?= $aluno->email ?></h4>

<?php include '../app/Views/layout/footer.php'; ?>