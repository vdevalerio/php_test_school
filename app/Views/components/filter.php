<?php
$filters      = $filters ?? [];
$filterFields = $filterFields ?? [];
$query        = $_GET;
unset($query['page']);
?>

<form method="GET" class="filters">
    <?php foreach ($filterFields as $field): ?>
        <?php
        $type  = $field['type'];
        $name  = $field['name'];
        $label = $field['label'];
        $value = $filters[$name] ?? '';
        ?>

        <?php if ($type === 'select'): ?>
            <div class="filters__group">
                <label for="filter_<?= $name ?>"><?= $label ?></label>
                <select
                    id="filter_<?= $name ?>"
                    name="<?= $name ?>"
                    class="filters__select"
                >
                    <option value="">Todos</option>
                    <?php foreach ($field['options'] as $option): ?>
                        <option
                            value="<?= $option['value'] ?>"
                            <?= $value == $option['value'] ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($option['label']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

        <?php elseif ($type === 'date_range'): ?>
            <div class="filters__group">
                <label><?= $label ?></label>
                <div class="filters__range">
                    <input
                        type="date"
                        name="<?= $name ?>_inicio"
                        value="<?= htmlspecialchars($filters[$name . '_inicio'] ?? '') ?>"
                        placeholder="De"
                    >
                    <span class="filters__range-sep">—</span>
                    <input
                        type="date"
                        name="<?= $name ?>_fim"
                        value="<?= htmlspecialchars($filters[$name . '_fim'] ?? '') ?>"
                        placeholder="Até"
                    >
                </div>
            </div>

        <?php elseif ($type === 'number_range'): ?>
            <div class="filters__group">
                <label><?= $label ?></label>
                <div class="filters__range">
                    <input
                        type="number"
                        name="<?= $name ?>_min"
                        value="<?= htmlspecialchars($filters[$name . '_min'] ?? '') ?>"
                        placeholder="Mín"
                        step="0.1"
                        min="0"
                    >
                    <span class="filters__range-sep">—</span>
                    <input
                        type="number"
                        name="<?= $name ?>_max"
                        value="<?= htmlspecialchars($filters[$name . '_max'] ?? '') ?>"
                        placeholder="Máx"
                        step="0.1"
                        min="0"
                    >
                </div>
            </div>

        <?php elseif ($type === 'text'): ?>
            <div class="filters__group">
                <label for="filter_<?= $name ?>"><?= $label ?></label>
                <input
                    type="text"
                    id="filter_<?= $name ?>"
                    name="<?= $name ?>"
                    value="<?= htmlspecialchars($value) ?>"
                    placeholder="Buscar..."
                >
            </div>
        <?php endif; ?>

    <?php endforeach; ?>

    <div class="filters__actions">
        <button type="submit">Filtrar</button>
        <a href="?">Limpar</a>
    </div>
</form>