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
                    <td><?= $cell ?></td>
                <?php endforeach; ?>
                <td><?php component('action-menu', $row['actions']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
