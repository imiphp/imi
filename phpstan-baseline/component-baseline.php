<?php

declare(strict_types=1);

$component = getenv('PHPSTAN_ANALYSE_COMPONENT_NAME');
$generateBaseline = getenv('PHPSTAN_GENERATE_BASELINE');

$defaultConfig = [
    'parameters' => [
        'resultCachePath' => "%tmpDir%/resultCache-imi-3-{$component}.php",
    ],
];

if (empty($component) || 'true' === $generateBaseline)
{
    return $defaultConfig;
}

$file = __DIR__ . "/baseline-{$component}.neon";

if (!file_exists($file))
{
    return $defaultConfig;
}

// echo $file, PHP_EOL;

use PHPStan\DependencyInjection\NeonAdapter;

$adapter = new NeonAdapter();

$config = $adapter->load($file);
$config['parameters']['resultCachePath'] = $defaultConfig['parameters']['resultCachePath'];

return $config;
