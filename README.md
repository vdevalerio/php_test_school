# Technical Test PHP - School


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
