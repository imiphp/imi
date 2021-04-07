<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\WebSocketServerWithRedisServerUtil\Tests;

use Swoole\Coroutine\Channel;
use Yurun\Util\HttpRequest;

/**
 * @testdox Imi\Swoole\Server\Server
 */
class ServerUtilTest extends BaseTest
{
    public function testGetServer(): void
    {
        $this->go(function () {
            $http = new HttpRequest();
            $response = $http->get($this->host . 'serverUtil/getServer');
            $this->assertEquals([
                'null'      => 'main',
                'main'      => 'main',
                'notFound'  => true,
            ], $response->json(true));
        });
    }

    public function testSendMessage(): void
    {
        $this->go(function () {
            $http = new HttpRequest();
            $response = $http->get($this->host . 'serverUtil/sendMessage');
            $this->assertEquals([
                'sendMessageAll'    => 1,
                'sendMessage1'      => 1,
                'sendMessage2'      => 1,
                'sendMessageRawAll' => 1,
                'sendMessageRaw1'   => 1,
                'sendMessageRaw2'   => 1,
            ], $response->json(true));
        });
    }

    public function testSend(): void
    {
        $this->go(function () {
            $th = null;
            $channel = new Channel(16);
            $waitChannel = new Channel(16);
            go(function () use ($waitChannel, $channel) {
                try
                {
                    $dataStr = json_encode([
                        'data'  => 'test',
                    ]);
                    $http = new HttpRequest();
                    $http->retry = 3;
                    $http->timeout = 10000;
                    $client = $http->websocket($this->host);
                    $this->assertTrue($client->isConnected());
                    $this->assertTrue($client->send(json_encode([
                        'action'    => 'login',
                        'username'  => 'testSend',
                    ])));
                    $recv = $client->recv();
                    $recvData = json_decode($recv, true);
                    $this->assertTrue($recvData['success'] ?? null, $client->getErrorCode() . '-' . $client->getErrorMessage());
                    $channel->push('test');
                    for ($i = 0; $i < 4; ++$i)
                    {
                        $recvResult = $client->recv();
                        $this->assertEquals($dataStr, $recvResult, $client->getErrorCode() . '-' . $client->getErrorMessage());
                    }
                    $client->close();
                    $waitChannel->push(1);
                }
                catch (\Throwable $th)
                {
                    $channel->push($th);
                    $waitChannel->push($th);
                }
            });
            $result = $channel->pop(30);
            $this->assertNotFalse($result);
            if ($result instanceof \Throwable)
            {
                throw $result;
            }
            $http = new HttpRequest();
            $response = $http->post($this->host . 'serverUtil/send', [
                'flag' => 'testSend',
            ], 'json');
            $result = $waitChannel->pop();
            $this->assertNotFalse($result);
            if ($result instanceof \Throwable)
            {
                throw $result;
            }
            $this->assertEquals([
                'sendByFlag'    => 1,
                'sendRawByFlag' => 1,
                'sendToAll'     => 1,
                'sendRawToAll'  => 1,
            ], $response->json(true));
        });
    }

    public function testSendToGroup(): void
    {
        $this->go(function () {
            $th = null;
            $waitChannel = new Channel(16);
            $func = function ($recvCount) use ($waitChannel) {
                $dataStr = json_encode([
                    'data'  => 'test',
                ]);
                $http = new HttpRequest();
                $http->retry = 3;
                $http->timeout = 10000;
                $client = $http->websocket($this->host);
                $this->assertTrue($client->isConnected());
                $this->assertTrue($client->send(json_encode([
                    'action'    => 'login',
                    'username'  => uniqid('', true),
                ])));
                $recv = $client->recv();
                $recvData = json_decode($recv, true);
                $this->assertTrue($recvData['success'] ?? null, $client->getErrorCode() . '-' . $client->getErrorMessage());
                $waitChannel->push(1);
                for ($i = 0; $i < $recvCount; ++$i)
                {
                    $recvResult = $client->recv();
                    $this->assertEquals($dataStr, $recvResult, $client->getErrorCode() . '-' . $client->getErrorMessage());
                }
                $client->close();
            };
            for ($i = 0; $i < 2; ++$i)
            {
                go(function () use ($func, $waitChannel) {
                    try
                    {
                        $func(2);
                        $waitChannel->push(1);
                    }
                    catch (\Throwable $th)
                    {
                        $waitChannel->push($th);
                    }
                });
            }
            $th = null;
            for ($i = 0; $i < 2; ++$i)
            {
                $result = $waitChannel->pop();
                $this->assertNotFalse($result);
                if ($result instanceof \Throwable)
                {
                    $th = $result;
                }
            }
            if (isset($th))
            {
                throw $th;
            }
            $http = new HttpRequest();
            $response = $http->get($this->host . 'serverUtil/sendToGroup');
            $th = null;
            for ($i = 0; $i < 2; ++$i)
            {
                $result = $waitChannel->pop();
                $this->assertNotFalse($result);
                if ($result instanceof \Throwable)
                {
                    $th = $result;
                }
            }
            if (isset($th))
            {
                throw $th;
            }
            $this->assertEquals([
                'sendToGroup'    => 1,
                'sendRawToGroup' => 1,
            ], $response->json(true));
        });
    }

    public function testClose(): void
    {
        $http1 = new HttpRequest();
        $http1->retry = 3;
        $http1->timeout = 10000;
        $client1 = $http1->websocket($this->host);
        $this->assertTrue($client1->isConnected());
        $this->assertTrue($client1->send(json_encode([
            'action'    => 'info',
        ])));
        $recv = $client1->recv();
        $recvData1 = json_decode($recv, true);
        $this->assertTrue(isset($recvData1['clientId']), 'Not found clientId');

        $http2 = new HttpRequest();
        $http2->retry = 3;
        $http1->timeout = 10000;
        $client2 = $http2->websocket($this->host);
        $this->assertTrue($client2->isConnected());
        $this->assertTrue($client2->send(json_encode([
            'action'    => 'login',
            'username'  => 'testClose',
        ])));
        $recv = $client2->recv();
        $recvData2 = json_decode($recv, true);
        $this->assertTrue($recvData2['success'] ?? null, 'Not found success');

        $http3 = new HttpRequest();
        $response = $http3->post($this->host . 'serverUtil/close', ['clientId' => $recvData1['clientId'], 'flag' => 'testClose']);
        $this->assertEquals([
            'clientId'   => 1,
            'flag'       => 1,
        ], $response->json(true));
        $this->assertEquals('', $client1->recv(1));
        $this->assertEquals('', $client2->recv(1));
    }
}
