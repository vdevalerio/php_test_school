<?php

namespace App\Controllers;

use App\Core\Response;
use App\Core\Sanitizer;
use App\Core\Validator;
use App\Exceptions\DatabaseException;
use App\Models\Nota;
use App\Models\Turma;

class NotaController
{
    public function index(): Response
    {
        $page           = max(1, (int) ($_GET['page'] ?? 1));
        $perPage        = (int) ($_GET['per_page'] ?? 10);
        $perPageOptions = [10, 25, 50, 100];
        $sort           = $_GET['sort'] ?? 'aluno_id';
        $direction      = $_GET['direction'] ?? 'asc';

        $allowedSorts = [
            'id', 'aluno_id', 'aluno_nome', 'turma_nome',
            'disciplina', 'nota', 'media_aluno', 'data_lancamento',
        ];
        $allowedDirs = ['asc', 'desc'];

        if (!in_array($sort, $allowedSorts)) {
            $sort = 'aluno_id';
        }

        if (!in_array($direction, $allowedDirs)) {
            $direction = 'asc';
        }

        $filters = [
            'turma_id'               => filter_var(
                $_GET['turma_id'] ?? '', FILTER_VALIDATE_INT
            ) ?: '',
            'data_lancamento_inicio' => Sanitizer::date(
                $_GET['data_lancamento_inicio'] ?? ''
            ) ?? '',
            'data_lancamento_fim'    => Sanitizer::date(
                $_GET['data_lancamento_fim'] ?? ''
            ) ?? '',
            'media_min'              => is_numeric(
                $_GET['media_min'] ?? ''
            ) ? (string) (float) $_GET['media_min'] : '',
            'media_max'              => is_numeric(
                $_GET['media_max'] ?? ''
            ) ? (string) (float) $_GET['media_max'] : '',
        ];

        $select = "
            notas.*,
            alunos.nome AS aluno_nome,
            turmas.nome AS turma_nome,
            AVG(notas.nota) OVER (PARTITION BY notas.aluno_id) AS media_aluno
        ";

        $innerBuilder = Nota::query()
            ->select($select)
            ->leftJoin('alunos', 'notas.aluno_id', '=', 'alunos.id')
            ->leftJoin('turmas', 'alunos.turma_id', '=', 'turmas.id');

        if (!empty($filters['turma_id'])) {
            $innerBuilder->where('turmas.id', '=', $filters['turma_id']);
        }

        if (!empty($filters['data_lancamento_inicio'])
            && !empty($filters['data_lancamento_fim']))
        {
            $innerBuilder->whereBetween(
                'notas.data_lancamento',
                $filters['data_lancamento_inicio'],
                $filters['data_lancamento_fim']
            );
        } elseif (!empty($filters['data_lancamento_inicio'])) {
            $innerBuilder->where(
                'notas.data_lancamento',
                '>=',
                $filters['data_lancamento_inicio']
            );
        } elseif (!empty($filters['data_lancamento_fim'])) {
            $innerBuilder->where(
                'notas.data_lancamento',
                '<=',
                $filters['data_lancamento_fim']
            );
        }

        $builder = $innerBuilder->fromSubquery();

        if (!empty($filters['media_min']) && !empty($filters['media_max'])) {
            $builder->whereBetween(
                'media_aluno',
                $filters['media_min'],
                $filters['media_max']
            );
        } elseif (!empty($filters['media_min'])) {
            $builder->where('media_aluno', '>=', $filters['media_min']);
        } elseif (!empty($filters['media_max'])) {
            $builder->where('media_aluno', '<=', $filters['media_max']);
        }

        $breaksGrouping = ['disciplina', 'nota', 'data_lancamento', 'id'];
        $grouped        = !in_array($sort, $breaksGrouping);
        $groupByAlunoId = $grouped && !in_array($sort, [
            'aluno_id', 'aluno_nome', 'media_aluno', 'turma_nome',
        ]);

        if ($groupByAlunoId) {
            $builder->orderBy('aluno_id', 'asc');
        }

        $pagination = $builder
            ->orderBy($sort, $direction)
            ->paginate($page, $perPage, $perPageOptions);

        $turmas = Turma::all();

        $filterFields = [
            [
                'type'    => 'select',
                'name'    => 'turma_id',
                'label'   => 'Turma',
                'options' => array_map(fn($t) => [
                    'value' => $t->id,
                    'label' => $t->nome,
                ], $turmas),
            ],
            [
                'type'  => 'date_range',
                'name'  => 'data_lancamento',
                'label' => 'Data de Lançamento',
            ],
            [
                'type'  => 'number_range',
                'name'  => 'media',
                'label' => 'Média',
            ],
        ];

        return Response::view('notas/index', [
            'pagination'   => $pagination,
            'sort'         => $sort,
            'direction'    => $direction,
            'grouped'      => $grouped,
            'filters'      => $filters,
            'filterFields' => $filterFields,
            'heading'      => 'Notas',
        ]);
    }

    public function create(): Response
    {
        return Response::view('notas/_form', [
            'action'      => '/notas',
            'method'      => 'POST',
            'nota'        => null,
            'submitLabel' => 'Criar nota',
        ]);
    }

    public function store(): Response
    {
        $validator = new Validator();
        if (!$validator->validate($_POST, [
            'aluno_id'        => 'required|integer',
            'disciplina'      => 'required|string|min_len:2|max_len:100',
            'nota'            => 'required|numeric|min:0|max:10',
            'data_lancamento' => 'date:Y-m-d',
        ])) {
            return Response::redirect(
                '/notas?error=' . urlencode($validator->firstError())
            );
        }

        try {
            Nota::create([
                'aluno_id'        => Sanitizer::int($_POST['aluno_id']),
                'disciplina'      => Sanitizer::string($_POST['disciplina']),
                'nota'            => Sanitizer::float($_POST['nota']),
                'data_lancamento' => Sanitizer::date(
                    $_POST['data_lancamento']
                ) ?? date('Y-m-d'),
            ]);
        } catch (\PDOException $e) {
            throw DatabaseException::fromPDO($e);
        }

        return Response::redirect($_SERVER['HTTP_REFERER'] ?? '/notas');
    }

    public function edit(string $id): Response
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if ($id === false || $id < 1) {
            http_response_code(404);
            return Response::view('errors/404', []);
        }

        $nota = Nota::find($id);
        if (!$nota) {
            http_response_code(404);
            return Response::view('errors/404', []);
        }

        return Response::view('notas/_form', [
            'nota'        => $nota,
            'action'      => "/notas/$id",
            'method'      => 'PUT',
            'submitLabel' => 'Atualizar nota',
        ]);
    }

    public function update(string $id): Response
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if ($id === false || $id < 1) {
            return Response::redirect(
                '/notas?error=' . urlencode('ID inválido.')
            );
        }

        $validator = new Validator();
        if (!$validator->validate($_POST, [
            'aluno_id'        => 'required|integer',
            'disciplina'      => 'required|string|min_len:2|max_len:100',
            'nota'            => 'required|numeric|min:0|max:10',
            'data_lancamento' => 'date:Y-m-d',
        ])) {
            return Response::redirect(
                '/notas?error=' . urlencode($validator->firstError())
            );
        }

        try {
            Nota::update($id, [
                'aluno_id'        => Sanitizer::int($_POST['aluno_id']),
                'disciplina'      => Sanitizer::string($_POST['disciplina']),
                'nota'            => Sanitizer::float($_POST['nota']),
                'data_lancamento' => Sanitizer::date(
                    $_POST['data_lancamento']
                ) ?? date('Y-m-d'),
            ]);
        } catch (\PDOException $e) {
            throw DatabaseException::fromPDO($e);
        }

        return Response::redirect($_SERVER['HTTP_REFERER'] ?? '/notas');
    }

    public function destroy(string $id): Response
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if ($id === false || $id < 1) {
            return Response::redirect(
                '/notas?error=' . urlencode('ID inválido.')
            );
        }

        try {
            Nota::delete($id);
        } catch (\PDOException $e) {
            throw DatabaseException::fromPDO($e);
        }

        return Response::redirect($_SERVER['HTTP_REFERER'] ?? '/notas');
    }
}