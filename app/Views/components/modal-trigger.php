<?php

$modalId  = 'modal-' . $id;
$btnId    = 'btn-' . $id;

if (empty($fetchUrl)) return;
?>

<?php if (isset($disabled) && $disabled): ?>
    <span
        class="btn <?= $variant ?? '' ?>"
        title="<?= htmlspecialchars($disabledMessage ?? 'Não disponível') ?>"
        style="opacity: 0.5; cursor: not-allowed; margin-bottom: 10px;"
    >
        <?= $disabledMessage ?>
    </span>
<?php return; ?>
<?php endif; ?>

<button
    id="<?= $btnId ?>"
    class="btn <?= $variant ?? '' ?>"
    data-modal="<?= $modalId ?>"
    data-fetch-url="<?= $fetchUrl ?>"
    <?= empty($fetchUrl) ? 'disabled title="URL não configurada"' : '' ?>
>
    <?= $label ?>
</button>

<div id="<?= $modalId ?>" class="modal" aria-hidden="true">
    <div class="modal-overlay" data-close-modal="<?= $modalId ?>"></div>
    <div class="modal-content">
        <p class="modal-content__loading">Carregando...</p>
    </div>
</div>