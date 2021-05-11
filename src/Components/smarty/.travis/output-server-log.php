<?php
$filename = dirname(__DIR__) . '/example/logs/' . date('Y-m-d') . '.log';
echo 'LogFile: ', $filename, PHP_EOL;
if(is_file($filename))
{
    echo file_get_contents($filename), PHP_EOL;
}
else
{
    echo 'File not found', PHP_EOL;
}
