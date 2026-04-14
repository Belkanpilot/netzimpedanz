<?php
declare(strict_types=1);

/**
 * Cache-Busting für CSS/JS: ?v=Änderungszeit der Datei.
 * Bei jedem Deploy/Änderung der Datei ändert sich die URL automatisch.
 */
function netz_asset(string $relativePath): string
{
    $base = dirname(__DIR__);
    $path = $base . '/' . ltrim($relativePath, '/');
    $v = is_file($path) ? (string) filemtime($path) : (string) time();

    return htmlspecialchars($relativePath . '?v=' . $v, ENT_QUOTES, 'UTF-8');
}
