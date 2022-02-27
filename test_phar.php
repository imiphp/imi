<?php

$output = getcwd() . '/test.phar';

@unlink('test.phar');

$phar = new Phar($output, 0, 'test.phar');
$phar->startBuffering();

$fileList = [];

for ($i = 0; $i < 2000; $i++) {
    $name = "tests/test{$i}.php";
    $phar->addFromString(
        $name,
        <<<PHP
    <?php

    \$test = [
        'a1'  => [],
        'a2'  => [],
        'a3'  => [],
        'a4'  => [],
        'a5'  => [],
        'a6'  => [],
        'a7'  => [],
        'a8'  => [],
        'a9'  => [],
        'a10'  => [],
    ];

    return [
        __FILE__ ,
        time(),
        getmypid(),
        mt_rand(),
        count(\$test),
    ];
    PHP
    );
    $fileList[] = $name;
}

$phar->addFromString(
    'swoole.php',
    <<<PHP
    <?php
    \Swoole\Runtime::enableCoroutine(true, SWOOLE_HOOK_ALL);

    \$http = new \Swoole\Http\Server("127.0.0.1", 12221);
    \$http->on('WorkerStart', function (\$server, \$worker_id) {
        Phar::loadPhar('test.phar');
        var_dump("Hello Worker. # \$worker_id");

        \$info = require 'phar://test.phar/tests/test666.php';
        var_dump(\$info);
    });
    \$http->on('request', function (\$request, \$response) {
        \$response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
    });
    \$http->start();
    PHP
);

$stud = <<<PHP
        #!/usr/bin/env php
        <?php

        Phar::mapPhar('test.phar');
        var_dump(__FILE__);
        require 'phar://test.phar/swoole.php';
        // \$info = require 'phar://test.phar/tests/test1.php';
        // var_dump(\$info);
        __HALT_COMPILER();
        PHP;

$phar->setStub($stud);

$phar->stopBuffering();
$phar->compressFiles(Phar::GZ);
