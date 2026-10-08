<?php declare(strict_types=1);

$projectDir = dirname(__DIR__, 4);

$loader = require $projectDir . '/vendor/autoload.php';
$loader->addPsr4('Tb\\', dirname(__DIR__) . '/src');
$loader->addPsr4('Tb\\Tests\\', __DIR__);
