<?php

function dd(mixed ...$vars): never
{
    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
    $file  = $trace['file'] ?? 'unknown file';
    $line  = $trace['line'] ?? 0;

    if (PHP_SAPI === 'cli') {
        echo "\n\033[1;33m{$file}:{$line}\033[0m\n";
        foreach ($vars as $var) {
            echo "\033[1;32m";
            var_dump($var);
            echo "\033[0m";
        }
        die();
    }

    $output = htmlspecialchars(
        implode("\n\n", array_map(fn($var) => print_r($var, true), $vars)),
        ENT_QUOTES
    );

    echo <<<HTML
    <style>
        .dd-wrapper {
            background: #1e1e1e;
            color: #d4d4d4;
            font-family: 'Fira Code', 'Courier New', monospace;
            font-size: 13px;
            padding: 16px 20px;
            margin: 10px;
            border-left: 4px solid #569cd6;
            border-radius: 4px;
            white-space: pre-wrap;
            word-break: break-all;
        }
        .dd-location {
            color: #569cd6;
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 12px;
        }
        .dd-value { color: #9cdcfe; }
    </style>
    <div class="dd-wrapper">
        <div class="dd-location">{$file}:{$line}</div>
        <div class="dd-value">{$output}</div>
    </div>
    HTML;

    die();
}

function component(string $name, array $props = []): void
{
    extract($props);
    require __DIR__ . "/Views/components/{$name}.php";
}

function closeModal(string $id): void {}