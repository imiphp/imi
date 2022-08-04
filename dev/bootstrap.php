<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Core\Configuration\Option;
use Rector\Set\ValueObject\LevelSetList;

ini_set('date.timezone', 'Asia/Shanghai');

function getRectorConfigCallback(string $path): callable
{
    // @phpstan-ignore-next-line
    return static function (RectorConfig $rectorConfig) use ($path): void {
        // get parameters
        // @phpstan-ignore-next-line
        $rectorConfig->paths([
            $path . '/src',
        ]);

        $rectorConfig->skip([
            '*/vendor/*',
            $path . '/src/Components/*',
            \Rector\Php71\Rector\FuncCall\CountOnNullRector::class,
            \Rector\Php73\Rector\FuncCall\JsonThrowOnErrorRector::class,
            \Rector\Php74\Rector\LNumber\AddLiteralSeparatorToNumberRector::class,
            \Rector\Php70\Rector\FuncCall\RandomFunctionRector::class,
        ]);

        $rectorConfig->bootstrapFiles([
            $path . '/vendor/autoload.php',
        ]);

        $rectorConfig->autoloadPaths([
            $path . '/src',
        ]);

        $rectorConfig->sets([LevelSetList::UP_TO_PHP_74]);
    };
}
