<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\WebSocketServer\Tests;

use Yurun\Util\HttpRequest;

/**
 * @testdox WebSocket
 */
class WSTest extends BaseTest
{
    /**
     * @testdox test
     */
    public function test(): void
    {
        $this->go(function () {
            $http = new HttpRequest();
            $http->retry = 3;
            $http->timeout = 10000;
            $client = $http->websocket($this->host);
            $this->assertTrue($client->isConnected());
            $this->assertTrue($client->send(json_encode([
                'action'    => 'login',
                'username'  => 'test',
            ])));
            $recv = $client->recv();
            // @phpstan-ignore-next-line
            $this->assertNotFalse($recv);
            // @phpstan-ignore-next-line
            $recvData = json_decode($recv, true);
            if (!isset($recvData['clientId']))
            {
                $this->assertTrue(false, $client->getErrorCode() . '-' . $client->getErrorMessage());
            }
            $clientId = $recvData['clientId'];
            $this->assertEquals([
                'success'                          => true,
                'username'                         => 'test',
                'middlewareData'                   => 'imi',
                'requestUri'                       => $this->host,
                'uri'                              => $this->host,
                'clientId'                         => $clientId,
                'getClientIdByFlag'                => [$clientId],
                'getFlagByClientId'                => 'test',
                'getClientIdsByFlags'              => ['test' => [$clientId]],
                'getFlagsByClientIds'              => [$clientId => 'test'],
            ], $recvData);
            $time = time();
            $this->assertTrue($client->send(json_encode([
                'action'    => 'send',
                'message'   => $time,
            ])));
            $recv = $client->recv();
            $this->assertEquals('test:' . $time, $recv, $client->getErrorCode() . '-' . $client->getErrorMessage());
            $client->close();

            // 重连逻辑
            $recvData = null;
            // 重试3次
            for ($i = 0; $i < 3; ++$i)
            {
                sleep(1);
                $http = new HttpRequest();
                $http->retry = 3;
                $http->timeout = 10000;
                $client = $http->websocket($this->host);
                $this->assertTrue($client->isConnected());
                $this->assertTrue($client->send(json_encode([
                    'action'    => 'reconnect',
                    'token'     => 'test',
                ])));
                $recv = $client->recv();
                if (false !== $recv)
                {
                    $recvData = json_decode($recv, true);
                    if (null !== $recvData && ($recvData['username'] ?? null))
                    {
                        break;
                    }
                }
            }
            $this->assertEquals([
                'success'   => true,
                'username'  => 'test',
            ], $recvData, $client->getErrorCode() . '-' . $client->getErrorMessage());

            $time = time();
            $this->assertTrue($client->send(json_encode([
                'action'    => 'send',
                'message'   => $time,
            ])));
            $recv = $client->recv();
            $this->assertEquals('test:' . $time, $recv, $client->getErrorCode() . '-' . $client->getErrorMessage());
            $client->close();
        });
    }

    public function testNotFound(): void
    {
        $this->go(function () {
            $http = new HttpRequest();
            $http->retry = 3;
            $http->timeout = 10000;
            $client = $http->websocket($this->host);
            $this->assertTrue($client->isConnected());
            $this->assertTrue($client->send(json_encode([
                'action'    => 'gg',
            ])));
            $recv = $client->recv();
            // @phpstan-ignore-next-line
            $this->assertEquals(json_encode('gg'), $recv, $client->getErrorCode() . '-' . $client->getErrorMessage());
            $client->close();
        });
    }

    public function testMatchHttpRoute(): void
    {
        $this->go(function () {
            $http = new HttpRequest();
            $http->retry = 3;
            $http->timeout = 10000;
            $client = $http->websocket($this->host);
            $this->assertTrue($client->isConnected());
            $this->assertTrue($client->send(json_encode([
                'action'    => 'test',
                'username'  => 'test',
            ])));
            $recv = $client->recv();
            // @phpstan-ignore-next-line
            $this->assertEquals(json_encode('gg'), $recv, $client->getErrorCode() . '-' . $client->getErrorMessage());
            $client->close();

            $client = $http->websocket($this->host . 'test');
            $this->assertTrue($client->isConnected());
            $recv = null;
            $this->assertTrue($client->send(json_encode([
                'action'    => 'test',
                'username'  => 'test',
            ])));
            $recv = $client->recv();
            $this->assertEquals(json_encode([
                'data'  => [
                    'action'    => 'test',
                    'username'  => 'test',
                ],
            ]), $recv, $client->getErrorCode() . '-' . $client->getErrorMessage());
            $client->close();
        });
    }

    public function testHttp(): void
    {
        $this->go(function () {
            $http = new HttpRequest();
            $http->timeout = 10000;
            $response = $http->get($this->host . 'http');
            $this->assertEquals('http', $response->body());
        });
    }
}
