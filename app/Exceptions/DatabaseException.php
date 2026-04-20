<?php

namespace App\Exceptions;

class DatabaseException extends \RuntimeException
{
    public static function fromPDO(\PDOException $e): self
    {
        return new self(
            message: 'Erro ao acessar o banco de dados.',
            code: (int) $e->getCode(),
            previous: $e
        );
    }
}
