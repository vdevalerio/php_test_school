<?php

namespace App\Core;

class Response
{
    private ?string $redirectUrl = null;
    private ?string $view        = null;
    private array $data          = [];
    private int $statusCode      = 200;

    public static function redirect(string $url, int $status = 302): static
    {
        $response              = new static();
        $response->redirectUrl = $url;
        $response->statusCode  = $status;

        return $response;
    }

    public static function view(string $path, array $data = []): static
    {
        $response       = new static();
        $response->view = $path;
        $response->data = $data;

        return $response;
    }

    public function send(): never
    {
        if ($this->redirectUrl !== null) {
            http_response_code($this->statusCode);
            header("Location: {$this->redirectUrl}");
            exit;
        }

        if ($this->view !== null) {
            extract($this->data);
            require "../app/Views/{$this->view}.php";
            exit;
        }

        exit;
    }

    public function getRedirectUrl(): ?string
    {
        return $this->redirectUrl;
    }

    public function getView(): ?string
    {
        return $this->view;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function isRedirect(): bool
    {
        return $this->redirectUrl !== null;
    }

    public function isView(): bool
    {
        return $this->view !== null;
    }
}