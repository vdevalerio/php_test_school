<?php if (empty($showUrl) && empty($editId) && empty($deleteUrl)) return; ?>

<div class="action-menu">
    <button
        type="button"
        class="action-menu__toggle"
        onclick="toggleActionMenu(this)"
    >
        Ações
    </button>

    <div class="action-menu__dropdown">
        <?php if (!empty($showUrl)): ?>
            <a href="<?= $showUrl ?>">Visualizar</a>
        <?php endif; ?>

        <?php if (!empty($editId)): ?>
            <?php component('modal-trigger', [
                'id'       => $editId,
                'label'    => 'Editar',
                'variant'  => 'primary',
                'fetchUrl' => $editFetchUrl,
            ]) ?>
        <?php endif; ?>

        <?php if (!empty($deleteUrl)): ?>
            <form
                method="POST"
                action="<?= $deleteUrl ?>"
            >
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit">Excluir</button>
            </form>
        <?php endif; ?>
    </div>
</div>
