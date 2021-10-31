<?php

declare(strict_types=1);

// EOF自动分包的客户端测试

Swoole\Coroutine\run(function () {
    $client = new Swoole\Coroutine\Client(\SWOOLE_SOCK_TCP);

    $client->set([
        'open_eof_split' => true,
        'package_eof'    => "\r\n",
    ]);

    // ------ 这里改成要连接的ip和端口 ------
    if (!$client->connect('127.0.0.1', 8082, 3))
    {
        throw new \RuntimeException("connect failed. Error: {$client->errCode}\n");
    }

    $client->send(json_encode([
        'action'    => 'send',
        'message'   => 'now:' . date('Y-m-d H:i:s'),
    ]) . "\r\n");

    $data = $client->recv();
    if ($data)
    {
        $data = json_decode($data);
        var_dump($data);
    }
    else
    {
        var_dump($data);
    }
});
