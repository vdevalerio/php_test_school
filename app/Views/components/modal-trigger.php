<?php
$modalId  = 'modal-' . $id;
$btnId    = 'btn-' . $id;
?>

<button id="<?= $btnId ?>" class="btn <?= $variant ?? '' ?>">
    <?= $label ?>
</button>

<div id="<?= $modalId ?>" class="modal">
    <div class="modal-overlay" onclick="closeModal('<?= $modalId ?>')"></div>
    <div class="modal-content">
    </div>
</div>

<script>
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
    document.body.style.overflow = '';
}

document.getElementById('<?= $btnId ?>').onclick = () => {
    fetch('<?= $fetchUrl ?>')
        .then(res => res.text())
        .then(html => {
            document.querySelector('#<?= $modalId ?> .modal-content').innerHTML = html;
            document.getElementById('<?= $modalId ?>').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        });
};
</script>