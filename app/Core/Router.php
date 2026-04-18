<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $uri, string $action): static
    {
        $this->routes[] = [
            'method' => 'GET',
            'uri'    => $uri,
            'action' => $action
        ];

        return $this;
    }

    public function post(string $uri, string $action): static
    {
        $this->routes[] = [
            'method' => 'POST',
            'uri'    => $uri,
            'action' => $action
        ];

        return $this;
    }

    public function put(string $uri, string $action): static
    {
        $this->routes[] = [
            'method' => 'PUT',
            'uri'    => $uri,
            'action' => $action
        ];

        return $this;
    }

    public function delete(string $uri, string $action): static
    {
        $this->routes[] = [
            'method' => 'DELETE',
            'uri'    => $uri,
            'action' => $action
        ];

        return $this;
    }

    public function dispatch(): void
    {
        $requestUri     = $this->resolveUri();
        $requestMethod  = $this->resolveMethod();

        foreach ($this->routes as $route) {
            if ($this->matchesRoute(
                $route,
                $requestMethod,
                $requestUri,
                $matches
            )) {
                $this->callAction($route['action'], $matches);
                return;
            }
        }

        $this->abort(404);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function toRegex(string $uri): string
    {
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $uri);

        return '#^' . $pattern . '$#';
    }

    private function resolveUri(): string
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    public function resolveMethod(): string
    {
        return strtoupper($_POST['_method'] ?? $_SERVER['REQUEST_METHOD']);
    }

    private function matchesRoute(
        array $route,
        string $method,
        string $uri,
        ?array &$matches
        ): bool
    {
        $pattern = $this->toRegex($route['uri']);

        return $route['method'] === $method
            && preg_match($pattern, $uri, $matches);
    }

    private function callAction(string $action, array $matches): void
    {
        $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

        [$controller, $method] = explode('@', $action);
        $class = "App\\Controllers\\$controller";

        (new $class)->$method(...array_values($params));
    }

    private function abort(int $code = 404): void
    {
        http_response_code($code);
        require __DIR__ . "/../../app/Views/{$code}.view.php";
        die();
    }
}