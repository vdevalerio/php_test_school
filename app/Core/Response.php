<?php

namespace App\Core;

class Response
{
    public static function redirect(string $url): never
    {
        header("Location: $url");
        exit;
    }
}