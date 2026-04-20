<?php

$exports  = $exports  ?? [];
$basePath = $basePath ?? '';
$query    = $query    ?? '';
?>

<?php if (!empty($exports)): ?>
    <div class="export-buttons">
        <?php foreach ($exports as $export):
            $url = $basePath . '/' . $export['format'];
            if ($query !== '') $url .= '?' . $query;
            $format = strtolower($export['format']);
        ?>
            <a
                href="<?= htmlspecialchars($url) ?>"
                class="export-btn export-btn--<?= $format ?>"
            >
                <span class="export-btn__icon"></span>
                <span><?= $export['label'] ?></span>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>