<?php declare(strict_types=1);

$projectAutoload = dirname(__DIR__, 4) . '/vendor/autoload.php';
$pluginAutoload = dirname(__DIR__) . '/vendor/autoload.php';

/** @var Composer\Autoload\ClassLoader $loader */
$loader = require is_file($projectAutoload) ? $projectAutoload : $pluginAutoload;
$loader->addPsr4('Tb\\', dirname(__DIR__) . '/src');
$loader->addPsr4('Tb\\Tests\\', __DIR__);
