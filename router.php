<?php
// Simple router for PHP built-in server to support pretty URLs
// Serves existing files directly; otherwise forwards to index.php with page param

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');
$docroot = rtrim($_SERVER['DOCUMENT_ROOT'] ?? __DIR__, '/\\');
$target = $docroot . $uri;

// Serve existing files or directories as-is
if ($uri !== '/' && (file_exists($target) || is_dir($target))) {
    return false; // Let the built-in server handle static assets or directories
}

// Map root to index
if ($uri === '/' || $uri === '' || $uri === '/index.php') {
    require __DIR__ . '/index.php';
    return true;
}

// Forward non-file requests to index.php with page param
$_GET['page'] = trim($uri, '/');
require __DIR__ . '/index.php';
return true;
?>