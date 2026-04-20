<h1><?= $heading ?></h1>

<?php if (!empty($_GET['error'])): ?>
<p class="alert-error"><?= htmlspecialchars($_GET['error']) ?></p>
<?php endif; ?>