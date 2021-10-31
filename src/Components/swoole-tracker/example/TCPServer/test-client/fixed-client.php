<?php

declare(strict_types=1);

use Swoole\Coroutine\Client;

// EOF自动分包的客户端测试

Swoole\Coroutine\run(function () {
    $client = new Client(\SWOOLE_SOCK_TCP);

    $client->set([
        'open_length_check'     => true,
        'package_length_type'   => 'N',
        'package_length_offset' => 0,       //第N个字节是包长度的值
        'package_body_offset'   => 4,       //第几个字节开始计算长度
        'package_max_length'    => 1024 * 1024,  //协议最大长度
    ]);

    // ------ 这里改成要连接的ip和端口 ------
    if (!$client->connect('127.0.0.1', 8082, 3))
    {
        throw new \RuntimeException("connect failed. Error: {$client->errCode}\n");
    }

    sendData($client, json_encode([
        'action'    => 'send',
        'message'   => 'now:' . date('Y-m-d H:i:s'),
    ]));

    $data = $client->recv();
    if ($data)
    {
        $data = json_decode(substr($data, 4));
        var_dump($data);
    }
    else
    {
        var_dump($data);
    }
});

/**
 * @param mixed $data
 *
 * @return mixed
 */
function sendData(Client $client, $data)
{
    $data = pack('N', \strlen($data)) . $data;

    return $client->send($data);
}
