<?php

namespace App\Core;

class Validator
{
    private array $errors = [];

    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $ruleString) {
            $value = $data[$field] ?? null;
            foreach (explode('|', $ruleString) as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }

        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): string
    {
        return array_values($this->errors)[0] ?? '';
    }

    private function applyRule(string $field, mixed $value, string $rule): void
    {
        [$ruleName, $param] = array_pad(explode(':', $rule, 2), 2, null);

        match ($ruleName) {
            'required' => $this->checkRequired($field, $value),
            'string'   => $this->checkString($field, $value),
            'integer'  => $this->checkInteger($field, $value),
            'numeric'  => $this->checkNumeric($field, $value),
            'email'    => $this->checkEmail($field, $value),
            'unique'   => $this->checkUnique($field, $value, $param),
            'min'      => $this->checkMin($field, $value, (float) $param),
            'max'      => $this->checkMax($field, $value, (float) $param),
            'min_len'  => $this->checkMinLen($field, $value, (int) $param),
            'max_len'  => $this->checkMaxLen($field, $value, (int) $param),
            'date'     => $this->checkDate($field, $value, $param ?? 'Y-m-d'),
            'in'       => $this->checkIn(
                $field,
                $value,
                explode(',', (string) $param)
            ),
            default    => null,
        };
    }

    private function checkRequired(string $field, mixed $value): void
    {
        if ($value === null || trim((string) $value) === '') {
            $this->errors[$field] = "O campo {$field} é obrigatório.";
        }
    }

    private function checkString(string $field, mixed $value): void
    {
        if ($value !== null && !is_string($value)) {
            $this->errors[$field] = "O campo {$field} deve ser texto.";
        }
    }

    private function checkInteger(string $field, mixed $value): void
    {
        if (
            $value !== null
            && trim((string) $value) !== ''
            && filter_var($value, FILTER_VALIDATE_INT) === false
        ) {
            $this->errors[$field] = "O campo {$field} deve ser um número inteiro.";
        }
    }

    private function checkNumeric(string $field, mixed $value): void
    {
        if (
            $value !== null
            && trim((string) $value) !== ''
            && !is_numeric(str_replace(',', '.', (string) $value))
        ) {
            $this->errors[$field] = "O campo {$field} deve ser numérico.";
        }
    }

    private function checkEmail(string $field, mixed $value): void
    {
        if (
            $value !== null
            && trim((string) $value) !== ''
            && filter_var($value, FILTER_VALIDATE_EMAIL) === false
        ) {
            $this->errors[$field] = "O campo {$field} deve ser um e-mail válido.";
        }
    }

    private function checkUnique(string $field, mixed $value, ?string $param): void
    {
        if (empty($value) || $param === null) return;

        [$tableColumn, $ignoreId] = array_pad(explode(':', $param, 2), 2, null);
        [$table, $column]         = explode('.', $tableColumn, 2);

        $db    = Database::getInstance();
        $sql   = "SELECT COUNT(*) FROM {$table} WHERE {$column} = ?";
        $binds = [$value];

        if ($ignoreId !== null) {
            $sql    .= ' AND id != ?';
            $binds[] = $ignoreId;
        }

        $count = (int) $db->query($sql, $binds)->fetchColumn();

        if ($count > 0) {
            $this->errors[$field] = "O valor informado para {$field} já está em uso.";
        }
    }

    private function checkMin(string $field, mixed $value, float $min): void
    {
        $normalized = str_replace(',', '.', (string) $value);
        if (
            $value !== null
            && is_numeric($normalized)
            && (float) $normalized < $min
        ) {
            $this->errors[$field] = "O campo {$field} deve ser no mínimo {$min}.";
        }
    }

    private function checkMax(string $field, mixed $value, float $max): void
    {
        $normalized = str_replace(',', '.', (string) $value);
        if (
            $value !== null
            && is_numeric($normalized)
            && (float) $normalized > $max
        ) {
            $this->errors[$field] = "O campo {$field} deve ser no máximo {$max}.";
        }
    }

    private function checkMinLen(string $field, mixed $value, int $min): void
    {
        if ($value !== null && mb_strlen((string) $value) < $min) {
            $this->errors[$field] = "O campo {$field} deve ter pelo menos {$min} caracteres.";
        }
    }

    private function checkMaxLen(string $field, mixed $value, int $max): void
    {
        if ($value !== null && mb_strlen((string) $value) > $max) {
            $this->errors[$field] = "O campo {$field} deve ter no máximo {$max} caracteres.";
        }
    }

    private function checkDate(string $field, mixed $value, string $format): void
    {
        if ($value === null || trim((string) $value) === '') {
            return;
        }
        $d = \DateTime::createFromFormat($format, (string) $value);
        if (!$d || $d->format($format) !== $value) {
            $this->errors[$field] = "O campo {$field} deve ser uma data no formato {$format}.";
        }
    }

    private function checkIn(string $field, mixed $value, array $allowed): void
    {
        if ($value !== null && !in_array($value, $allowed, strict: true)) {
            $this->errors[$field] = "O valor de {$field} não é permitido.";
        }
    }
}
