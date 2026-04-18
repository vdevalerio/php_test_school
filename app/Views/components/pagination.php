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

    <?php for ($page = 1; $page <= $last; $page++): ?>
        <?php $query['page'] = $page; ?>
        <a href="<?= htmlspecialchars('?' . http_build_query($query)) ?>"
            class="
                pagination__btn
                <?= $page === $current ? 'pagination__btn--active' : '' ?>
            "
        >
            <?= $page ?>
        </a>
    <?php endfor; ?>

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