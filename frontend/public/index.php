<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../../backend/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../../backend/vendor/autoload.php';

(require_once __DIR__.'/../../backend/bootstrap/app.php')
    ->handleRequest(Request::capture());
