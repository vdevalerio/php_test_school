<?php

namespace App\Core;

class Sanitizer
{
    public static function string(mixed $value): string
    {
        return trim((string) ($value ?? ''));
    }

    public static function int(mixed $value): int
    {
        return (int) $value;
    }

    public static function float(mixed $value): float
    {
        return (float) str_replace(',', '.', (string) $value);
    }

    public static function email(mixed $value): string
    {
        return strtolower(trim((string) ($value ?? '')));
    }

    public static function date(mixed $value, string $format = 'Y-m-d'): ?string
    {
        if (!$value || trim((string) $value) === '') {
            return null;
        }
        $d = \DateTime::createFromFormat($format, trim((string) $value));
        return $d ? $d->format($format) : null;
    }

    public static function text(mixed $value): string
    {
        return trim(strip_tags((string) ($value ?? '')));
    }
}
