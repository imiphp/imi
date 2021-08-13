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
                'sendMessage1'      => 1,
                'sendMessage2'      => 2,
                'sendMessageRawAll' => 2,
                'sendMessageRaw1'   => 1,
                'sendMessageRaw2'   => 2,
            ], $response->json(true));
        });
    }

    public function testSend(): void
    {
        $this->go(function () {
            $th = null;
            $channel = new Channel(16);
            $func = function ($index, $recvCount) use ($channel) {
                $dataStr = json_encode([
                    'data'  => 'test',
                ]);
                do
                {
                    echo 'try get workerId ', $index, \PHP_EOL;
                    $http = new HttpRequest();
                    $http->retry = 3;
                    $http->timeout = 10000;
                    $client = $http->websocket($this->host);
                    $this->assertTrue($client->isConnected());
                    $this->assertTrue($client->send(json_encode([
                        'action'    => 'info',
                    ])));
                    $recv = $client->recv();
                    $this->assertNotFalse($recv);
                    $recvData = json_decode($recv, true);
                    if (!isset($recvData['clientId']))
                    {
                        $this->assertTrue(false, $client->getErrorCode() . '-' . $client->getErrorMessage());
                    }
                } while ($index !== $recvData['workerId']);
                // @phpstan-ignore-next-line
                $channel->push([$index => $recvData['clientId']]);
                for ($i = 0; $i < $recvCount; ++$i)
                {
                    $recvResult = $client->recv();
                    $this->assertEquals($dataStr, $recvResult, $client->getErrorCode() . '-' . $client->getErrorMessage());
                }
                $client->close();
            };
            $waitChannel = new Channel(16);
            go(function () use ($func, $channel, $waitChannel) {
                try
                {
                    $func(0, 6);
                    $waitChannel->push(1);
                }
                catch (\Throwable $th)
                {
                    $channel->push($th);
                    $waitChannel->push($th);
                }
            });
            go(function () use ($func, $channel, $waitChannel) {
                try
                {
                    $func(1, 4);
                    $waitChannel->push(1);
                }
                catch (\Throwable $th)
                {
                    $channel->push($th);
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
                    $http->timeout = 10000;
                    $client = $http->websocket($this->host);
                    $this->assertTrue($client->isConnected());
                    $this->assertTrue($client->send(json_encode([
                        'action'    => 'login',
                        'username'  => 'testSend',
                    ])));
                    $recv = $client->recv();
                    $this->assertNotFalse($recv);
                    // @phpstan-ignore-next-line
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
            $clientIds = [];
            $th = null;
            for ($i = 0; $i < 3; ++$i)
            {
                $result = $channel->pop(30);
                $this->assertNotFalse($result);
                if (\is_array($result))
                {
                    $clientIds[key($result)] = current($result);
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
            ksort($clientIds);
            $this->assertCount(2, $clientIds);
            $http = new HttpRequest();
            $response = $http->post($this->host . 'serverUtil/send', [
                'clientIds'  => $clientIds,
                'flag'       => 'testSend',
            ], 'json');
            $th = null;
            for ($i = 0; $i < 3; ++$i)
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
        }, null, 3);
    }

    public function testSendToGroup(): void
    {
        $this->go(function () {
            $dataStr = json_encode([
                'data'  => 'test',
            ]);
            do
            {
                echo 'try get workerId 0', \PHP_EOL;
                $http1 = new HttpRequest();
                $http1->retry = 3;
                $http1->timeout = 10000;
                $client1 = $http1->websocket($this->host);
                $this->assertTrue($client1->isConnected());
                $this->assertTrue($client1->send(json_encode([
                    'action'    => 'info',
                ])));
                $recv = $client1->recv();
                $recvData = json_decode($recv, true);
                $this->assertTrue(isset($recvData['clientId']), $client1->getErrorCode() . '-' . $client1->getErrorMessage());
            } while (0 !== $recvData['workerId']);
            $this->assertTrue($client1->send(json_encode([
                'action'    => 'login',
                'username'  => uniqid('', true),
            ])));
            $recv = $client1->recv();
            // @phpstan-ignore-next-line
            $recvData = json_decode($recv, true);
            $this->assertTrue($recvData['success'] ?? null, $client1->getErrorCode() . '-' . $client1->getErrorMessage());

            do
            {
                echo 'try get workerId 1', \PHP_EOL;
                $http2 = new HttpRequest();
                $http2->retry = 3;
                $http2->timeout = 10000;
                $client2 = $http2->websocket($this->host);
                $this->assertTrue($client2->isConnected());
                $this->assertTrue($client2->send(json_encode([
                    'action'    => 'info',
                ])));
                $recv = $client2->recv();
                $recvData = json_decode($recv, true);
                $this->assertTrue(isset($recvData['clientId']), $client2->getErrorCode() . '-' . $client2->getErrorMessage());
            } while (1 !== $recvData['workerId']);
            $this->assertTrue($client2->send(json_encode([
                'action'    => 'login',
                'username'  => uniqid('', true),
            ])));
            $recv = $client2->recv();
            // @phpstan-ignore-next-line
            $recvData = json_decode($recv, true);
            $this->assertTrue($recvData['success'] ?? null, $client2->getErrorCode() . '-' . $client2->getErrorMessage());

            do
            {
                $http = new HttpRequest();
                $response = $http->get($this->host . 'serverUtil/info');
                $data = $response->json(true);
            } while (0 !== ($data['workerId'] ?? null));
            $response = $http->get($this->host . 'serverUtil/sendToGroup');
            $this->assertEquals([
                'groupClientIdCount'   => 2,
                'sendToGroup'          => 2,
                'sendRawToGroup'       => 2,
            ], $response->json(true));

            for ($i = 0; $i < 2; ++$i)
            {
                $recvResult = $client1->recv();
                $this->assertEquals($dataStr, $recvResult, $client1->getErrorCode() . '-' . $client1->getErrorMessage());
                $recvResult = $client2->recv();
                $this->assertEquals($dataStr, $recvResult, $client2->getErrorCode() . '-' . $client2->getErrorMessage());
            }
            $client1->close();
            $client2->close();
        }, null, 3);
    }

    public function testExists(): void
    {
        $this->go(function () {
            do
            {
                echo 'try get workerId 0', \PHP_EOL;
                $http1 = new HttpRequest();
                $http1->retry = 3;
                $http1->timeout = 10000;
                $client1 = $http1->websocket($this->host);
                $this->assertTrue($client1->isConnected());
                $this->assertTrue($client1->send(json_encode([
                    'action'    => 'info',
                ])));
                $recv = $client1->recv();
                $recvData = json_decode($recv, true);
                $this->assertTrue(isset($recvData['clientId']), $client1->getErrorCode() . '-' . $client1->getErrorMessage());
            } while (0 !== $recvData['workerId']);
            $this->assertTrue($client1->send(json_encode([
                'action'    => 'info',
            ])));
            $recv = $client1->recv();
            $recvData1 = json_decode($recv, true);
            $this->assertTrue(isset($recvData1['clientId']), 'Not found clientId');

            do
            {
                echo 'try get workerId 1', \PHP_EOL;
                $http2 = new HttpRequest();
                $http2->retry = 3;
                $http2->timeout = 10000;
                $client2 = $http2->websocket($this->host);
                $this->assertTrue($client2->isConnected());
                $this->assertTrue($client2->send(json_encode([
                    'action'    => 'info',
                ])));
                $recv = $client2->recv();
                $recvData = json_decode($recv, true);
                $this->assertTrue(isset($recvData['clientId']), $client2->getErrorCode() . '-' . $client2->getErrorMessage());
            } while (0 !== $recvData['workerId']);
            $this->assertTrue($client2->send(json_encode([
                'action'    => 'login',
                'username'  => 'testExists',
            ])));
            $recv = $client2->recv();
            $recvData2 = json_decode($recv, true);
            $this->assertTrue($recvData2['success'] ?? null, 'Not found success');

            do
            {
                $http3 = new HttpRequest();
                $response = $http3->get($this->host . 'serverUtil/info');
                $data = $response->json(true);
            } while (1 !== ($data['workerId'] ?? null));
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
            do
            {
                echo 'try get workerId 0', \PHP_EOL;
                $http1 = new HttpRequest();
                $http1->retry = 3;
                $http1->timeout = 10000;
                $client1 = $http1->websocket($this->host);
                $this->assertTrue($client1->isConnected());
                $this->assertTrue($client1->send(json_encode([
                    'action'    => 'info',
                ])));
                $recv = $client1->recv();
                $recvData = json_decode($recv, true);
                $this->assertTrue(isset($recvData['clientId']), $client1->getErrorCode() . '-' . $client1->getErrorMessage());
            } while (0 !== $recvData['workerId']);
            $this->assertTrue($client1->send(json_encode([
                'action'    => 'info',
            ])));
            $recv = $client1->recv();
            $recvData1 = json_decode($recv, true);
            $this->assertTrue(isset($recvData1['clientId']), 'Not found clientId');

            do
            {
                echo 'try get workerId 1', \PHP_EOL;
                $http2 = new HttpRequest();
                $http2->retry = 3;
                $http2->timeout = 10000;
                $client2 = $http2->websocket($this->host);
                $this->assertTrue($client2->isConnected());
                $this->assertTrue($client2->send(json_encode([
                    'action'    => 'info',
                ])));
                $recv = $client2->recv();
                $recvData = json_decode($recv, true);
                $this->assertTrue(isset($recvData['clientId']), $client2->getErrorCode() . '-' . $client2->getErrorMessage());
            } while (0 !== $recvData['workerId']);
            $this->assertTrue($client2->send(json_encode([
                'action'    => 'login',
                'username'  => 'testClose',
            ])));
            $recv = $client2->recv();
            // @phpstan-ignore-next-line
            $recvData2 = json_decode($recv, true);
            $this->assertTrue($recvData2['success'] ?? null, 'Not found success');

            do
            {
                $http3 = new HttpRequest();
                $response = $http3->get($this->host . 'serverUtil/info');
                $data = $response->json(true);
            } while (1 !== ($data['workerId'] ?? null));
            // @phpstan-ignore-next-line
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
