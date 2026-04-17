<?php
?>
<div class="action-menu">
    <button type="button" class="action-menu__toggle" onclick="toggleActionMenu(this)">
        Ações
    </button>
    <div class="action-menu__dropdown">
        <?php if (!empty($showUrl)): ?>
            <a href="<?= $showUrl ?>">Visualizar</a>
        <?php endif; ?>
        <?php component('modal-trigger', [
            'id'       => $editId,
            'label'    => 'Editar',
            'variant'  => 'primary',
            'fetchUrl' => $editFetchUrl,
        ]) ?>
        <form method="POST" action="<?= $deleteUrl ?>">
            <input type="hidden" name="_method" value="DELETE">
            <button type="submit">Excluir</button>
        </form>
    </div>
</div>
