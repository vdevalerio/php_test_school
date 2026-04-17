<?php

use App\Models\Aluno;

include '../app/Views/layout/header.php';

include '../app/Views/layout/nav.php';
include '../app/Views/layout/banner.php';

?>

<?php component('modal-trigger', [
    'id' => 'criarNota',
    'label' => 'Criar Nota',
    'variant' => 'primary',
    'fetchUrl' => '/notas/create',
]) ?>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Aluno</th>
            <th>Disciplina</th>
            <th>Nota</th>
            <th>Data de Lançamento</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($notas as $nota): ?>
            <tr>
                <td><?php echo $nota['id']; ?></td>
                <td><?php echo Aluno::find($nota['aluno_id'])->nome; ?></td>
                <td><?php echo $nota['disciplina']; ?></td>
                <td><?php echo $nota['nota']; ?></td>
                <td><?php echo $nota['data_lancamento']; ?></td>
                <td>
                    <div class="action-menu">
                        <button
                            type="button"
                            class="action-menu__toggle"
                            onclick="toggleActionMenu(this)"
                        >
                            Ações
                        </button>
                        <div class="action-menu__dropdown">
                            <?php component('modal-trigger', [
                                'id' => 'editarNota-' . $nota['id'],
                                'label' => 'Editar',
                                'variant' => 'primary',
                                'fetchUrl' => '/notas/' . $nota['id'] . '/edit',
                            ]) ?>
                            <form method="POST" action="/notas/<?= $nota['id'] ?>">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit">Excluir</button>
                            </form>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../app/Views/layout/footer.php'; ?>