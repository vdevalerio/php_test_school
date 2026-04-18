<?php

$current  = $pagination['current_page'];
$last     = $pagination['last_page'];
$total    = $pagination['total'];
$perPage  = $pagination['per_page'];

$firstOfPage = ($current - 1) * $perPage + 1;
$lastOfPage  = min($current * $perPage, $total);
$info        = "{$firstOfPage}–{$lastOfPage} de {$total}";

$query = $_GET;
$query['page'] = $current - 1;
$prevUrl       = '?' . http_build_query($query);
$query['page'] = $current + 1;
$nextUrl       = '?' . http_build_query($query);

$canGoPrev = $current > 1;
$canGoNext = $current < $last;
?>

<nav class="pagination" aria-label="Paginação">
    <span class="pagination__info">
        <?= $info ?>
    </span>

    <a href="<?= $canGoPrev ? htmlspecialchars($prevUrl) : '' ?>"
        <?= !$canGoPrev ? 'aria-disabled="true"' : '' ?>
        class="
            pagination__btn
            <?= !$canGoPrev ? 'pagination__btn--disabled' : '' ?>
        "
    >
        &laquo; Anterior
    </a>

    <select id="page" name="page" onchange="window.location.href = this.value">
        <?php for ($page = 1; $page <= $last; $page++): ?>
            <?php $query['page'] = $page; ?>
            <option value="?<?= htmlspecialchars(http_build_query($query)) ?>"
                <?= $page == $current ? 'selected' : '' ?>
            >
                <?= $page ?>
            </option>
        <?php endfor; ?>
    </select>

    <a href="<?= $canGoNext ? htmlspecialchars($nextUrl) : '#' ?>"
        <?= !$canGoNext ? 'aria-disabled="true"' : '' ?>
        class="
            pagination__btn
            <?= !$canGoNext ? 'pagination__btn--disabled' : '' ?>
        "
    >
        Próximo &raquo;
    </a>
</nav>