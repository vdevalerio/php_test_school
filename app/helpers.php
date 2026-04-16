<?php

function dd($data) {
    echo "<pre>";
    var_dump($data);
    echo "</pre>";
    die();
}

function component(string $name, array $props = []): void
{
    extract($props);
    require __DIR__ . "/Views/components/{$name}.php";
}

function closeModal(string $id): void {}