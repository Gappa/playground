<?php

// Hack for PHP Development server!!!
$_SERVER['SCRIPT_NAME'] = '/index.php';

$container = require __DIR__ . '/../app/bootstrap.php';
$container->getByType(Contributte\Middlewares\Application\IApplication::class)->run();
