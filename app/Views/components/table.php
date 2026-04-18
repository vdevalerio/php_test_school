<table>
    <thead>
        <tr>
            <?php foreach ($columns as $column): ?>
                <th><?= $column ?></th>
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