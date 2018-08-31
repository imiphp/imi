<?php
namespace Imi;

$isPhar = 'phar://' === @substr(__DIR__, 0, 7);

if($isPhar)
{
    require dirname(__DIR__) . '/vendor/autoload.php';
}
