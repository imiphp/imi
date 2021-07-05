<?php

declare(strict_types=1);

namespace Imi\Workerman\Test\ChannelServerUtilServer\Tests\WebSocket;

use Wrench\Client;
use Yurun\Util\HttpRequest;

/**
 * @testdox Imi\Workerman\Server\Server
 */
class ServerUtilTest extends BaseTest
{
    public function testGetServer(): void
    {
        $this->go(function () {
            $http = new HttpRequest();
            $response = $http->get($this->httpHost . 'serverUtil/getServer');
            $this->assertEquals([
                'null'      => 'http',
                'http'      => 'http',
                'notFound'  => true,
            ], $response->json(true));
        });
    }

    public function testSendMessage(): void
    {
        $this->go(function () {
            $http = new HttpRequest();
            $response = $http->get($this->httpHost . 'serverUtil/sendMessage');
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
            $client1 = new Client($this->host, $this->httpHost);
            $this->assertTrue($client1->connect());
            $this->assertTrue($client1->sendData(json_encode([
                'action'    => 'info',
            ])));
            $recvData1 = $client1->receive();
            $recv = reset($recvData1)->getPayload();
            $recvData1 = json_decode($recv, true);
            $this->assertTrue(isset($recvData1['clientId']));

            $client2 = new Client($this->host, $this->httpHost);
            $this->assertTrue($client2->connect());
            $this->assertTrue($client2->sendData(json_encode([
                'action'    => 'info',
            ])));
            $recvData2 = $client2->receive();
            $recv = reset($recvData2)->getPayload();
            $recvData2 = json_decode($recv, true);
            $this->assertTrue(isset($recvData2['clientId']));

            $client3 = new Client($this->host, $this->httpHost);
            $this->assertTrue($client3->connect());
            $this->assertTrue($client3->sendData(json_encode([
                'action'    => 'login',
                'username'  => 'testSend',
            ])));
            $recvData3 = $client3->receive();
            $recv = reset($recvData3)->getPayload();
            $recvData3 = json_decode($recv, true);
            $this->assertTrue($recvData3['success'] ?? null);

            $http = new HttpRequest();
            $response = $http->post($this->httpHost . 'serverUtil/send', [
                'flag' => 'testSend',
            ], 'json');
            $this->assertEquals([
                'sendByFlag'    => 1,
                'sendRawByFlag' => 1,
                // Workerman LocalServerUtil 跨进程只支持返回 1/0
                'sendToAll'     => 1,
                'sendRawToAll'  => 1,
            ], $response->json(true));

            for ($i = 0; $i < 2; ++$i)
            {
                $this->assertNotFalse($client1->receive());
                $this->assertNotFalse($client2->receive());
            }
            for ($i = 0; $i < 4; ++$i)
            {
                $this->assertNotFalse($client3->receive());
            }

            $client1->disconnect();
            $client2->disconnect();
            $client3->disconnect();
        });
    }

    public function testSendToGroup(): void
    {
        $this->go(function () {
            /** @var Client[] $clients */
            $clients = [];
            for ($i = 0; $i < 2; ++$i)
            {
                $clients[] = $client = new Client($this->host, $this->httpHost);
                $this->assertTrue($client->connect());
                $this->assertTrue($client->sendData(json_encode([
                    'action'    => 'login',
                    'username'  => uniqid('', true),
                ])));
                $recvData = $client->receive();
                $recv = reset($recvData)->getPayload();
                $recvData = json_decode($recv, true);
                $this->assertTrue($recvData['success'] ?? null);
            }

            $http = new HttpRequest();
            $response = $http->get($this->httpHost . 'serverUtil/sendToGroup');

            $this->assertEquals([
                'sendToGroup'    => 1,
                'sendRawToGroup' => 1,
            ], $response->json(true));

            for ($i = 0; $i < 2; ++$i)
            {
                foreach ($clients as $client)
                {
                    $this->assertNotFalse($client->receive());
                }
            }

            foreach ($clients as $client)
            {
                $client->disconnect();
            }
        });
    }

    public function testClose(): void
    {
        $client1 = new Client($this->host, $this->httpHost);
        $this->assertTrue($client1->connect());
        $this->assertTrue($client1->sendData(json_encode([
            'action'    => 'login',
            'username'  => 'testClose',
        ])));
        $recvData = $client1->receive();
        $recv = reset($recvData)->getPayload();
        $recvData1 = json_decode($recv, true);
        $this->assertTrue($recvData1['success'] ?? null, 'Not found success');

        $http3 = new HttpRequest();
        $response = $http3->post($this->httpHost . 'serverUtil/close', ['flag' => 'testClose']);
        $this->assertEquals([
            'flag' => 1,
        ], $response->json(true));
        $this->assertEquals('', $client1->receive());
    }
}
