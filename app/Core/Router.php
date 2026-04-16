<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $uri, string $action): static
    {
        $this->routes[] = [
            'method' => 'GET',
            'uri' => $uri,
            'action' => $action
        ];
        return $this;
    }

    public function post(string $uri, string $action): static
    {
        $this->routes[] = [
            'method' => 'POST',
            'uri' => $uri,
            'action' => $action
        ];
        return $this;
    }

    public function put(string $uri, string $action): static
    {
        $this->routes[] = [
            'method' => 'PUT',
            'uri' => $uri,
            'action' => $action
        ];
        return $this;
    }

    public function delete(string $uri, string $action): static
    {
        $this->routes[] = [
            'method' => 'DELETE',
            'uri' => $uri,
            'action' => $action
        ];
        return $this;
    }

    public function dispatch(): void
    {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestMethod = strtoupper($_POST['_method'] ?? $_SERVER['REQUEST_METHOD']);

        foreach ($this->routes as $route) {
            $pattern = $this->toRegex($route['uri']);

            if ($route['method'] === $requestMethod && preg_match($pattern, $requestUri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                [$controller, $method] = explode('@', $route['action']);
                $class = "App\\Controllers\\$controller";
                (new $class)->$method(...array_values($params));
                return;
            }
        }

        $this->abort(404);
    }

    private function toRegex(string $uri): string
    {
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $uri);
        return '#^' . $pattern . '$#';
    }

    private function abort(int $code = 404): void
    {
        http_response_code($code);
        require __DIR__ . "/../../app/Views/{$code}.view.php";
        die();
    }
}