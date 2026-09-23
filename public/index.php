<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

// Published at https://www.escm.mg/copil while this front controller lives in
// /copil/public. Without this, Laravel drops the prefix and /copil redirects to /connexion.
$scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));

if (str_contains($scriptName, '/copil/public/')) {
    $_SERVER['SCRIPT_NAME'] = '/copil/index.php';
    $_SERVER['PHP_SELF'] = '/copil/index.php';

    $requestUri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
    $path = parse_url($requestUri, PHP_URL_PATH);

    if ($path === '/copil') {
        $query = parse_url($requestUri, PHP_URL_QUERY);
        $_SERVER['REQUEST_URI'] = '/copil/'.(is_string($query) && $query !== '' ? '?'.$query : '');
    }
}

$app->handleRequest(Request::capture());
