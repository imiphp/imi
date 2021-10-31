<?php

declare(strict_types=1);

Swoole\Coroutine\run(function () {
    $client = new Swoole\Coroutine\Client(\SWOOLE_SOCK_UDP);
    $client->connect('127.0.0.1', 8083);

    $client->send(json_encode([
        'action'    => 'hello',
        'format'    => 'Y-m-d H:i:s',
    ]));
    var_dump($client->recv());

    $client->send(json_encode([
        'action'    => 'hello',
        'format'    => 'c',
    ]));
    var_dump($client->recv());
});
