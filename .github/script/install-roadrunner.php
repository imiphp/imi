<?php

declare(strict_types=1);
$argv = $_SERVER['argv'];
unset($argv[0]);

$cmd = \dirname(__DIR__, 2) . '/src/Components/roadrunner/vendor/bin/rr get-binary ' . implode(' ', $argv);

$count = 12;
for ($i = 0; $i < $count; ++$i)
{
    passthru($cmd, $code);
    if (0 === $code)
    {
        return;
    }
    sleep(10);
}
exit($code);
