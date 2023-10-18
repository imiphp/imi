<?php
declare(strict_types=1);

$component = getenv('PHPSTAN_ANALYSE_COMPONENT_NAME');
$generateBaseline = getenv('PHPSTAN_GENERATE_BASELINE');

if (empty($component) || 'true' === $generateBaseline)
{
    return [];
}

$file = __DIR__ . "/baseline-{$component}.neon";

if (!file_exists($file))
{
    return [];
}

// echo $file, PHP_EOL;

use PHPStan\DependencyInjection\NeonAdapter;

$adapter = new NeonAdapter();

return $adapter->load($file);
