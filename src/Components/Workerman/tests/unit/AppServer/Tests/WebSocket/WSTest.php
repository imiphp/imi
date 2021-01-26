<?php

declare(strict_types=1);

namespace Imi\Workerman\Test\AppServer\Tests\WebSocket;

use Wrench\Client;
use Yurun\Util\HttpRequest;

/**
 * @testdox WebSocket
 */
class WSTest extends BaseTest
{
    /**
     * @testdox test
     */
    public function test()
    {
        $this->go(function () {
            // $http = new HttpRequest();
            // $http->retry = 3;
            // $http->timeout = 3000;
            // $http->connectTimeout = 3000;
            // $client = $http->websocket($this->host);
            $client = new Client($this->host, $this->host);
            $this->assertTrue($client->connect());
            $this->assertTrue($client->sendData(json_encode([
                'action'    => 'login',
                'username'  => 'test',
            ])));
            $recvData = $client->receive();
            $recv = reset($recvData)->getPayload();
            $this->assertNotFalse($recv);
            $recvData = json_decode($recv, true);
            if (!isset($recvData['token']))
            {
                $this->assertTrue(false, 'Not found token');
            }
            $token = $recvData['token'];
            $fd = $recvData['fd'];
            $this->assertEquals([
                'success'        => true,
                'middlewareData' => 'imi',
                'requestUri'     => $this->host,
                'uri'            => $this->host,
                'token'          => $token,
                'fd'             => $fd,
                'getFdByFlag'    => $fd,
                'getFlagByFd'    => $token,
                'getFdsByFlags'  => [$token => $fd],
                'getFlagsByFds'  => [$fd => $token],
            ], $recvData);
            $time = time();
            $this->assertTrue($client->sendData(json_encode([
                'action'    => 'send',
                'message'   => $time,
            ])));
            $recvData = $client->receive();
            $recv = reset($recvData)->getPayload();
            $this->assertEquals('test:' . $time, $recv);
            $client->disconnect();

            // 重连逻辑
            // $http = new HttpRequest();
            // $http->retry = 3;
            // $http->timeout = 3000;
            // $http->connectTimeout = 3000;
            // $client = $http->websocket($this->host);
            $client = new Client($this->host, $this->host);
            $this->assertTrue($client->connect());

            // 重试3次
            for ($i = 0; $i < 3; ++$i)
            {
                $this->assertTrue($client->sendData(json_encode([
                    'action'    => 'reconnect',
                    'token'     => $token,
                ])));
                $recvData = $client->receive();
                $recv = reset($recvData)->getPayload();
                if (false !== $recv)
                {
                    $recvData = json_decode($recv, true);
                    if (null !== $recvData)
                    {
                        break;
                    }
                }
                sleep(1);
            }
            $this->assertEquals([
                'success'   => true,
                'username'  => 'test',
            ], $recvData);

            $time = time();
            $this->assertTrue($client->sendData(json_encode([
                'action'    => 'send',
                'message'   => $time,
            ])));
            $recvData = $client->receive();
            $recv = reset($recvData)->getPayload();
            $this->assertEquals('test:' . $time, $recv);
            $client->disconnect();
        });
    }

    public function testNotFound()
    {
        $this->go(function () {
            // $http = new HttpRequest();
            // $http->retry = 3;
            // $http->timeout = 3000;
            // $http->connectTimeout = 3000;
            // $client = $http->websocket($this->host);
            $client = new Client($this->host, $this->host);
            $this->assertTrue($client->connect());
            $this->assertTrue($client->sendData(json_encode([
                'action'    => 'gg',
            ])));
            $recvData = $client->receive();
            $recv = reset($recvData)->getPayload();
            $this->assertEquals(json_encode('gg'), $recv);
            $client->disconnect();
        });
    }

    public function testMatchHttpRoute()
    {
        $this->go(function () {
            // $http = new HttpRequest();
            // $http->retry = 3;
            // $http->timeout = 3000;
            // $http->connectTimeout = 3000;
            // $client = $http->websocket($this->host);
            $client = new Client($this->host, $this->host);
            $this->assertTrue($client->connect());
            $this->assertTrue($client->sendData(json_encode([
                'action'    => 'test',
                'username'  => 'test',
            ])));
            $recvData = $client->receive();
            $recv = reset($recvData)->getPayload();
            $this->assertEquals(json_encode('gg'), $recv);
            $client->disconnect();

            // $client = $http->websocket($this->host . 'test');
            $client = new Client($this->host . 'test', $this->host);
            $this->assertTrue($client->connect());
            $this->assertTrue($client->sendData(json_encode([
                'action'    => 'test',
                'username'  => 'test',
            ])));
            $recvData = $client->receive();
            $recv = reset($recvData)->getPayload();
            $this->assertEquals(json_encode([
                'data'  => [
                    'action'    => 'test',
                    'username'  => 'test',
                ],
            ]), $recv);
            $client->disconnect();
        });
    }
}
