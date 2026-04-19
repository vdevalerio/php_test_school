<?php

$currentSort      = $sort;
$currentDirection = $direction;
$query            = $_GET;
?>

<table class="data-table">
    <thead>
        <tr>
            <?php foreach ($columns as $column): ?>
                <?php
                $label   = is_array($column) ? $column['label'] : $column;
                $sortKey = is_array($column) ? ($column['sort'] ?? null) : null;

                if ($sortKey):
                    $nextDir = ($currentSort === $sortKey
                        && $currentDirection === 'asc')
                        ? 'desc'
                        : 'asc';

                    $colQuery              = $_GET;
                    $colQuery['sort']      = $sortKey;
                    $colQuery['direction'] = $nextDir;
                    $colQuery['page']      = 1;
                    $url                   = '?' . http_build_query($colQuery);

                    $isActive = $currentSort === $sortKey;
                    $arrow = $isActive
                        ? ($currentDirection === 'asc' ? ' ▲' : ' ▼')
                        : '';
                ?>
        <th class="<?= $isActive ? 'data-table__th--active' : '' ?>">
            <a href="<?= htmlspecialchars($url) ?>"><?= $label . $arrow ?></a>
        </th>
    <?php else: ?>
        <th><?= $label ?></th>
    <?php endif; ?>
<?php endforeach; ?>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $row): ?>
            <tr>
                <?php foreach ($row['cells'] as $cell): ?>
                    <?php if (
                        is_array($cell)
                        && isset($cell['format'])
                        && $cell['value'] instanceof DateTime
                    ): ?>
                        <td><?= $cell['value']->format($cell['format']) ?></td>
                    <?php elseif ($cell instanceof DateTime): ?>
                        <td><?= $cell->format('d/m/Y H:i') ?></td>
                    <?php else: ?>
                        <td><?= $cell ?></td>
                    <?php endif; ?>
                <?php endforeach; ?>
                <td><?php component('action-menu', $row['actions']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if (!empty($pagination)): ?>
    <?php component('pagination', ['pagination' => $pagination]) ?>
<?php endif; ?>