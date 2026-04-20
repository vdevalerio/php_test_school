<?php include __DIR__ . '/../layout/header.php'; ?>
<?php include __DIR__ . '/../layout/nav.php'; ?>

<h1>Erro interno</h1>
<p><?= htmlspecialchars(
    $message ?? 'Ocorreu um erro inesperado. Tente novamente mais tarde.'
) ?></p>

<?php include __DIR__ . '/../layout/footer.php'; ?>
