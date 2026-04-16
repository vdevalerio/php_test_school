<?php

namespace App\Core;

class Router
{
    public static function get($uri, $action)
    {
        $request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if ($request_uri === $uri) {
            [$controller, $method] = explode('@', $action);

            $class = "App\\Controllers\\$controller";
            (new $class)->$method();
        } else {
            self::abort();
        }
    }

    private static function abort($code = 404) {
        http_response_code($code);
        require "../app/Views/{$code}.view.php";
        die();
    }
}