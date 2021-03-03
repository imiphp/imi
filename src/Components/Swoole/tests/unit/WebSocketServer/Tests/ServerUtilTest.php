<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\WebSocketServer\Tests;

use Swoole\Coroutine\Channel;
use Yurun\Util\HttpRequest;

/**
 * @testdox Imi\Swoole\Server\Server
 */
class ServerUtilTest extends BaseTest
{
    public function testGetServer()
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

    public function testSendMessage()
    {
        $this->go(function () {
            $http = new HttpRequest();
            $response = $http->get($this->host . 'serverUtil/sendMessage');
            $this->assertEquals([
                'sendMessageAll'    => 2,
                'sendMessage1'      => 1,
                'sendMessage2'      => 2,
                'sendMessageRawAll' => 2,
                'sendMessageRaw1'   => 1,
                'sendMessageRaw2'   => 2,
            ], $response->json(true));
        });
    }

    public function testSend()
    {
        $this->go(function () {
            for ($_ = 0; $_ < 3; ++$_)
            {
                $th = null;
                $channel = new Channel(1);
                try
                {
                    $th = null;
                    $channel = new Channel(16);
                    $func = function (int $recvCount) use ($channel) {
                        $dataStr = json_encode([
                            'data'  => 'test',
                        ]);
                        $http = new HttpRequest();
                        $http->retry = 3;
                        $http->timeout = 3000;
                        $http->connectTimeout = 3000;
                        $client = $http->websocket($this->host);
                        $this->assertTrue($client->isConnected());
                        $this->assertTrue($client->send(json_encode([
                            'action'    => 'info',
                        ])));
                        $recv = $client->recv();
                        $this->assertNotFalse($recv);
                        $recvData = json_decode($recv, true);
                        if (!isset($recvData['fd']))
                        {
                            $this->assertTrue(false, 'Not found fd');
                        }
                        $channel->push($recvData['fd']);
                        for ($i = 0; $i < $recvCount; ++$i)
                        {
                            $this->assertEquals($dataStr, $client->recv(10));
                        }
                        $client->close();
                    };
                    $waitChannel = new Channel(16);
                    go(function () use ($func, $waitChannel) {
                        try
                        {
                            $func(6);
                            $waitChannel->push(1);
                        }
                        catch (\Throwable $th)
                        {
                            $waitChannel->push($th);
                        }
                    });
                    go(function () use ($func, $waitChannel) {
                        try
                        {
                            $func(4);
                            $waitChannel->push(1);
                        }
                        catch (\Throwable $th)
                        {
                            $waitChannel->push($th);
                        }
                    });
                    go(function () use ($waitChannel, $channel) {
                        try
                        {
                            $dataStr = json_encode([
                                'data'  => 'test',
                            ]);
                            $http = new HttpRequest();
                            $http->retry = 3;
                            $http->timeout = 3000;
                            $http->connectTimeout = 3000;
                            $client = $http->websocket($this->host);
                            $this->assertTrue($client->isConnected());
                            $this->assertTrue($client->send(json_encode([
                                'action'    => 'login',
                                'username'  => 'testSend',
                            ])));
                            $recv = $client->recv();
                            $recvData = json_decode($recv, true);
                            $this->assertTrue($recvData['success'] ?? null, 'Not found success');
                            $channel->push('test');
                            for ($i = 0; $i < 2; ++$i)
                            {
                                $this->assertEquals($dataStr, $client->recv(10));
                            }
                            $client->close();
                            $waitChannel->push(1);
                        }
                        catch (\Throwable $th)
                        {
                            $waitChannel->push($th);
                        }
                    });
                    $fds = [];
                    for ($i = 0; $i < 3; ++$i)
                    {
                        $result = $channel->pop(10);
                        $this->assertNotFalse($result);
                        if (\is_int($result))
                        {
                            $fds[] = $result;
                        }
                    }
                    $this->assertCount(2, $fds);
                    $http = new HttpRequest();
                    $response = $http->post($this->host . 'serverUtil/send', [
                        'fds'  => $fds,
                        'flag' => 'testSend',
                    ], 'json');
                    $this->assertEquals([
                        'send1'         => 0,
                        'send2'         => 1,
                        'send3'         => 2,
                        'sendByFlag'    => 1,
                        'sendRaw1'      => 0,
                        'sendRaw2'      => 1,
                        'sendRaw3'      => 2,
                        'sendRawByFlag' => 1,
                        'sendToAll'     => 3,
                        'sendRawToAll'  => 3,
                    ], $response->json(true));
                    $th = null;
                    for ($i = 0; $i < 3; ++$i)
                    {
                        $result = $waitChannel->pop();
                        if (false === $result)
                        {
                            break;
                        }
                        elseif ($result instanceof \Throwable)
                        {
                            $th = $result;
                        }
                    }
                    if (isset($th))
                    {
                        throw $th;
                    }
                    break;
                }
                catch (\Throwable $th)
                {
                    sleep(1);
                }
                finally
                {
                    $channel->close();
                }
            }
            if (isset($th))
            {
                throw $th;
            }
        });
    }

    public function testSendToGroup()
    {
        $this->go(function () {
            for ($_ = 0; $_ < 3; ++$_)
            {
                $th = null;
                $waitChannel = new Channel(1);
                try
                {
                    $th = null;
                    $waitChannel = new Channel(16);
                    $func = function (int $recvCount) use ($waitChannel) {
                        $dataStr = json_encode([
                            'data'  => 'test',
                        ]);
                        $http = new HttpRequest();
                        $http->retry = 3;
                        $http->timeout = 3000;
                        $http->connectTimeout = 3000;
                        $client = $http->websocket($this->host);
                        $this->assertTrue($client->isConnected());
                        $this->assertTrue($client->send(json_encode([
                            'action'    => 'login',
                            'username'  => uniqid('', true),
                        ])));
                        $recv = $client->recv();
                        $this->assertNotFalse($recv);
                        $recvData = json_decode($recv, true);
                        $this->assertTrue($recvData['success'] ?? null, 'Not found success');
                        $waitChannel->push(1);
                        for ($i = 0; $i < $recvCount; ++$i)
                        {
                            $this->assertEquals($dataStr, $client->recv(10));
                        }
                        $client->close();
                    };
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
                    for ($i = 0; $i < 2; ++$i)
                    {
                        $result = $waitChannel->pop();
                        if (false === $result)
                        {
                            break;
                        }
                        elseif ($result instanceof \Throwable)
                        {
                            throw $result;
                        }
                    }
                    $http = new HttpRequest();
                    $response = $http->get($this->host . 'serverUtil/sendToGroup');
                    $this->assertEquals([
                        'sendToGroup'       => 2,
                        'sendRawToGroup'    => 2,
                    ], $response->json(true));
                    $th = null;
                    for ($i = 0; $i < 2; ++$i)
                    {
                        $result = $waitChannel->pop();
                        if (false === $result)
                        {
                            break;
                        }
                        elseif ($result instanceof \Throwable)
                        {
                            $th = $result;
                        }
                    }
                    if (isset($th))
                    {
                        throw $th;
                    }
                    break;
                }
                catch (\Throwable $th)
                {
                    sleep(1);
                }
                finally
                {
                    $waitChannel->close();
                }
            }
            if (isset($th))
            {
                throw $th;
            }
        });
    }

    public function testClose()
    {
        $http1 = new HttpRequest();
        $http1->retry = 3;
        $http1->timeout = 3000;
        $http1->connectTimeout = 3000;
        $client1 = $http1->websocket($this->host);
        $this->assertTrue($client1->isConnected());
        $this->assertTrue($client1->send(json_encode([
            'action'    => 'info',
        ])));
        $recv = $client1->recv();
        $recvData1 = json_decode($recv, true);
        $this->assertTrue(isset($recvData1['fd']), 'Not found fd');

        $http2 = new HttpRequest();
        $http2->retry = 3;
        $http2->timeout = 3000;
        $http2->connectTimeout = 3000;
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
        $response = $http3->post($this->host . 'serverUtil/close', ['fd' => $recvData1['fd'], 'flag' => 'testClose']);
        $this->assertEquals([
            'fd'   => 1,
            'flag' => 1,
        ], $response->json(true));
        $this->assertEquals('', $client1->recv());
        $this->assertEquals('', $client2->recv());
    }
}
