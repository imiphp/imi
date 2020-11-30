<?php

declare(strict_types=1);

namespace Imi;

$isPhar = 'phar://' === @substr(__DIR__, 0, 7);

if ($isPhar)
{
    $file = \dirname(__DIR__) . '/vendor/autoload.php';
    if (is_file($file))
    {
        require $file;
    }
}
