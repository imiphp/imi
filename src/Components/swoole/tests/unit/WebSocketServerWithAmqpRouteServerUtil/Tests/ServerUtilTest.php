<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\WebSocketServerWithAmqpRouteServerUtil\Tests;

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
            $dataStr = json_encode([
                'data'  => 'test',
            ]);
            $https = [];
            $clients = [];
            $clientIds = [];

            for ($i = 0; $i < 2; ++$i)
            {
                do
                {
                    echo 'try get workerId ', $i, \PHP_EOL;
                    $https[$i] = $http = new HttpRequest();
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
                }
                while ($i !== $recvData['workerId']);
                $clients[] = $client;
                $clientIds[] = $recvData['clientId'];
            }

            $https[] = $http = new HttpRequest();
            $http->retry = 3;
            $http->timeout = 10000;
            $client = $http->websocket($this->host);
            $this->assertTrue($client->isConnected());
            $group = uniqid('', true);
            $this->assertTrue($client->send(json_encode([
                'action'    => 'login',
                'username'  => ($flag = 'testSend' . time()),
                'group'     => $group,
            ])));
            $recv = $client->recv();
            $this->assertNotFalse($recv);
            // @phpstan-ignore-next-line
            $recvData = json_decode($recv, true);
            $this->assertTrue($recvData['success'] ?? null, $client->getErrorCode() . '-' . $client->getErrorMessage());
            $clients[] = $client;

            for ($workerId = 0; $workerId < 2; ++$workerId)
            {
                do
                {
                    $http = new HttpRequest();
                    $response = $http->get($this->host . 'serverUtil/info');
                    $data = $response->json(true);
                }
                while ($workerId !== ($data['workerId'] ?? null));

                $response = $http->post($this->host . 'serverUtil/send', [
                    'clientIds'  => $clientIds,
                    'flag'       => $flag,
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
                    'sendToAll'     => 1,
                    'sendRawToAll'  => 1,
                ], $response->json(true));

                // recv
                $client = $clients[0];
                for ($i = 0; $i < 6; ++$i)
                {
                    $recvResult = $client->recv();
                    $this->assertEquals($dataStr, $recvResult, $i . '-' . $client->getErrorCode() . '-' . $client->getErrorMessage());
                }

                $client = $clients[1];
                for ($i = 0; $i < 4; ++$i)
                {
                    $recvResult = $client->recv();
                    $this->assertEquals($dataStr, $recvResult, $i . '-' . $client->getErrorCode() . '-' . $client->getErrorMessage());
                }

                $client = $clients[2];
                for ($i = 0; $i < 4; ++$i)
                {
                    $recvResult = $client->recv();
                    $this->assertEquals($dataStr, $recvResult, $i . '-' . $client->getErrorCode() . '-' . $client->getErrorMessage());
                }
            }
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
            }
            while (0 !== $recvData['workerId']);
            $group = uniqid('', true);
            $this->assertTrue($client1->send(json_encode([
                'action'    => 'login',
                'username'  => uniqid('', true),
                'group'     => $group,
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
            }
            while (1 !== $recvData['workerId']);
            $this->assertTrue($client2->send(json_encode([
                'action'    => 'login',
                'username'  => uniqid('', true),
                'group'     => $group,
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
            }
            while (0 !== ($data['workerId'] ?? null));
            $response = $http->get($this->host . 'serverUtil/sendToGroup', ['group' => $group]);
            $this->assertEquals([
                'groupClientIdCount'   => 1,
                'sendToGroup'          => 1,
                'sendRawToGroup'       => 1,
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
            }
            while (0 !== $recvData['workerId']);
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
            }
            while (0 !== $recvData['workerId']);
            $group = uniqid('', true);
            $this->assertTrue($client2->send(json_encode([
                'action'    => 'login',
                'username'  => ($flag = 'testExists' . time()),
                'group'     => $group,
            ])));
            $recv = $client2->recv();
            $recvData2 = json_decode($recv, true);
            $this->assertTrue($recvData2['success'] ?? null, 'Not found success');

            do
            {
                $http3 = new HttpRequest();
                $response = $http3->get($this->host . 'serverUtil/info');
                $data = $response->json(true);
            }
            while (1 !== ($data['workerId'] ?? null));
            $response = $http3->post($this->host . 'serverUtil/exists', ['clientId' => $recvData1['clientId'], 'flag' => $flag]);
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
            }
            while (0 !== $recvData['workerId']);
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
            }
            while (0 !== $recvData['workerId']);
            $group = uniqid('', true);
            $this->assertTrue($client2->send(json_encode([
                'action'    => 'login',
                'username'  => ($flag = 'testClose' . time()),
                'group'     => $group,
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
            }
            while (1 !== ($data['workerId'] ?? null));
            // @phpstan-ignore-next-line
            $response = $http3->post($this->host . 'serverUtil/close', ['clientId' => $recvData1['clientId'], 'flag' => $flag]);
            $data = $response->json(true);
            $this->assertGreaterThan(0, $data['clientId']);
            $this->assertGreaterThan(0, $data['flag']);
            $this->assertEquals('', $client1->recv(1));
            $this->assertEquals('', $client2->recv(1));
        }, null, 3);
    }
}
