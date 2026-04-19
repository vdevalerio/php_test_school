<?php
$current        = $pagination['current_page'];
$last           = $pagination['last_page'];
$total          = $pagination['total'];
$perPage        = $pagination['per_page'];
$perPageOptions = $pagination['per_page_options'] ?? [];

$firstOfPage = ($current - 1) * $perPage + 1;
$lastOfPage  = min($current * $perPage, $total);
$info        = "{$firstOfPage}–{$lastOfPage} de {$total}";

$canGoPrev = $current > 1;
$canGoNext = $current < $last;

$base             = $_GET;
$base['per_page'] = $perPage;

$base['page'] = $current - 1;
$prevUrl      = '?' . http_build_query($base);

$base['page'] = $current + 1;
$nextUrl      = '?' . http_build_query($base);
?>

<nav class="pagination" aria-label="Paginação">
    <span class="pagination__info"><?= $info ?></span>

    <div class="pagination__controls">
        <?php if ($canGoPrev): ?>
            <a
                href="<?= htmlspecialchars($prevUrl) ?>"
                class="pagination__btn"
                aria-label="Página anterior"
            >&laquo; Anterior</a>
        <?php else: ?>
            <span
                class="pagination__btn pagination__btn--disabled"
                aria-disabled="true"
            >&laquo; Anterior</span>
        <?php endif; ?>

        <select
            class="pagination__select"
            aria-label="Ir para página"
            onchange="window.location.href = this.value"
        >
            <?php for ($page = 1; $page <= $last; $page++): ?>
                <?php
                $base['page'] = $page;
                $url          = '?' . http_build_query($base);
                ?>
                <option
                    value="<?= htmlspecialchars($url) ?>"
                    <?= $page === $current ? 'selected' : '' ?>
                ><?= $page ?></option>
            <?php endfor; ?>
        </select>

        <?php if ($canGoNext): ?>
            <a
                href="<?= htmlspecialchars($nextUrl) ?>"
                class="pagination__btn"
                aria-label="Próxima página"
            >Próximo &raquo;</a>
        <?php else: ?>
            <span
                class="pagination__btn pagination__btn--disabled"
                aria-disabled="true"
            >Próximo &raquo;</span>
        <?php endif; ?>
    </div>

    <?php if (!empty($perPageOptions)): ?>
        <div class="pagination__perpage">
            <span class="pagination__label">Por página:</span>
            <select
                class="pagination__select"
                aria-label="Itens por página"
                onchange="window.location.href = this.value"
            >
                <?php foreach ($perPageOptions as $option): ?>
                    <?php
                    $base['page']     = 1;
                    $base['per_page'] = $option;
                    $url              = '?' . http_build_query($base);
                    ?>
                    <option
                        value="<?= htmlspecialchars($url) ?>"
                        <?= $option === $perPage ? 'selected' : '' ?>
                    ><?= $option ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    <?php else: ?>
        <span></span>
    <?php endif; ?>
</nav>