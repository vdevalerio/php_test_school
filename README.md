# Technical Test PHP - School

## TODOs

- [ ] Adicionar escopo na criação de aluno/nota
    - Dentro de uma turma: pré-selecionar turma/aluno corrente
    - A partir do menu externo: usar placeholder "Selecionar" em vez do primeiro registro

---

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
