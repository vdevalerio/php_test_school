# Technical Test PHP - School

## TODOs

- [ ] Adicionar paginação
    - [ ] Preciso de um query builder pra adicionar paginacao na interna das models
    - [ ] Extrair e centralizar esse codigo de criacao de "collections":
      ```php
      return array_map(function (array $data) {
          $obj = new $this->modelClass();
          foreach ($data as $key => $value) {
              $obj->$key = $obj->castValue($key, $value);
          }
          return $obj;
      }, $rows);
      ```
    - [ ] Refatorar sprintf para usar inteiros nos locais corretos
    ```
    $sql    = sprintf(
            'SELECT * FROM %s%s LIMIT %s OFFSET %s', // Deveria ser %d para limit e offset
            $this->table,
            $this->buildWhereClause(),
            $perPage,
            $offset
        );
    ```
- [ ] Adicionar ordenação
- [ ] Adicionar filtros
- [ ] Formatar date/datetime nas tabelas e datepicker
- [ ] Ajustar tamanho da modal ao conteúdo
- [ ] Handle modal when fetch link is null
- [ ] Validação de inputs (ex: nota fora do range dispara PDOException)
- [ ] Disparar e tratar exceção quando valor do form está vazio
- [ ] Adicionar escopo na criação de aluno/nota
    - Dentro de uma turma: pré-selecionar turma/aluno corrente
    - A partir do menu externo: usar placeholder "Selecionar" em vez do primeiro registro

---

## Melhorias de arquitetura

### Dependency injection no Model

Em vez de instanciar `Database` dentro do model, injetar via setter:

```php
abstract class Model
{
    protected static string $table;
    protected static Database $db;

    public static function setDatabase(Database $db): void
    {
        static::$db = $db;
    }
}
```

```php
$db = new Database();
Model::setDatabase($db);
```

### View helper

`require` dentro de método é um antipadrão — o template herda o escopo da função, o que é frágil. O correto é passar variáveis explicitamente:

```php
// app/helpers.php
function view(string $path, array $data = []): void
{
    extract($data);
    require "../app/Views/{$path}.php";
}
```

```php
public function show(string $id): void
{
    $aluno = Aluno::find($id);
    view('alunos/show', ['aluno' => $aluno, 'heading' => 'Aluno']);
}
```

### Nullsafe operator no form compartilhado

No `create`, `$nota` não é definida — acessar `$nota->aluno_id` estoura mesmo com `??`, pois `??` protege contra propriedade inexistente, não contra acesso em `null`.

Definir explicitamente na controller:

```php
public function create(): void
{
    $nota        = null; // ✅
    $action      = '/notas';
    $method      = 'POST';
    $submitLabel = 'Criar';
    require '../app/Views/notas/form.php';
}
```

E no template usar nullsafe:

```php
$selected = $alunoItem['id'] == ($nota?->aluno_id ?? null) ? 'selected' : '';
```
