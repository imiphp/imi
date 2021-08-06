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
                'sendMessageAll'    => 2,
                'sendMessage1'      => 2,
                'sendMessage2'      => 2,
                'sendMessageRawAll' => 2,
                'sendMessageRaw1'   => 2,
                'sendMessageRaw2'   => 2,
            ], $response->json(true));
        });
    }

    public function testSend(): void
    {
        $this->go(function () {
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

            $http2 = new HttpRequest();
            $response = $http2->post($this->host . 'serverUtil/send', [
                'flag' => 'testSend',
            ], 'json');

            for ($i = 0; $i < 4; ++$i)
            {
                $recvResult = $client->recv();
                $this->assertEquals($dataStr, $recvResult, $client->getErrorCode() . '-' . $client->getErrorMessage());
            }

            $this->assertEquals([
                'sendByFlag'    => 1,
                'sendRawByFlag' => 1,
                'sendToAll'     => 1,
                'sendRawToAll'  => 1,
            ], $response->json(true));
        }, null, 3);
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
                'sendToGroup'    => 2,
                'sendRawToGroup' => 2,
            ], $response->json(true));
        }, null, 3);
    }

    public function testExists(): void
    {
        $this->go(function () {
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
                'username'  => 'testExists',
            ])));
            $recv = $client2->recv();
            $recvData2 = json_decode($recv, true);
            $this->assertTrue($recvData2['success'] ?? null, 'Not found success');

            $http3 = new HttpRequest();
            $response = $http3->post($this->host . 'serverUtil/exists', ['clientId' => $recvData1['clientId'], 'flag' => 'testExists']);
            $this->assertEquals([
                'clientId'   => true,
                'flag'       => true,
            ], $response->json(true));
            $client1->close();
            $client2->close();
        }, null, 3);
    }

    public function testClose(): void
    {
        $this->go(function () {
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
        }, null, 3);
    }
}
