<?php

declare(strict_types=1);

/**
 * Return the application URL prefix for static assets.
 *
 * SCRIPT_NAME should be a URL path, but some local preview handlers can
 * expose an absolute URL. Parse it first so a scheme cannot become a host.
 */
function swiftOrderAssetBaseFromScriptName(string $scriptName): string
{
    $scriptPath = parse_url($scriptName, PHP_URL_PATH);

    if (!is_string($scriptPath) || $scriptPath === '') {
        $scriptPath = '/';
    }

    $scriptPath = str_replace('\\', '/', $scriptPath);
    $directory = dirname($scriptPath);

    if ($directory === '.' || $directory === '/') {
        return '';
    }

    return rtrim($directory, '/');
}
