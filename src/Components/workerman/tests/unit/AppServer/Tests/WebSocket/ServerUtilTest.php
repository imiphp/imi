<?php

declare(strict_types=1);

namespace Imi\Workerman\Test\AppServer\Tests\WebSocket;

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

            $clientIds = [
                $recvData1['clientId'],
                $recvData2['clientId'],
            ];

            $http = new HttpRequest();
            $response = $http->post($this->httpHost . 'serverUtil/send', [
                'clientIds'  => $clientIds,
                'flag'       => 'testSend',
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

            for ($i = 0; $i < 6; ++$i)
            {
                $this->assertNotFalse($client1->receive());
            }
            for ($i = 0; $i < 4; ++$i)
            {
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
                'sendToGroup'    => 2,
                'sendRawToGroup' => 2,
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

    public function testExists(): void
    {
        $client1 = new Client($this->host, $this->httpHost);
        $this->assertTrue($client1->connect());
        $this->assertTrue($client1->sendData(json_encode([
            'action'    => 'info',
        ])));
        $recvData = $client1->receive();
        $recv = reset($recvData)->getPayload();
        $recvData1 = json_decode($recv, true);
        $this->assertTrue(isset($recvData1['clientId']), 'Not found clientId');

        $client2 = new Client($this->host, $this->httpHost);
        $this->assertTrue($client2->connect());
        $this->assertTrue($client2->sendData(json_encode([
            'action'    => 'login',
            'username'  => 'testExists',
        ])));
        $recvData = $client2->receive();
        $recv = reset($recvData)->getPayload();
        $recvData2 = json_decode($recv, true);
        $this->assertTrue($recvData2['success'] ?? null, 'Not found success');

        $http3 = new HttpRequest();
        $response = $http3->post($this->httpHost . 'serverUtil/exists', ['clientId' => $recvData1['clientId'], 'flag' => 'testExists']);
        $this->assertEquals([
            'clientId' => true,
            'flag'     => true,
        ], $response->json(true));
        $client1->disconnect();
        $client2->disconnect();
    }

    public function testClose(): void
    {
        $client1 = new Client($this->host, $this->httpHost);
        $this->assertTrue($client1->connect());
        $this->assertTrue($client1->sendData(json_encode([
            'action'    => 'info',
        ])));
        $recvData = $client1->receive();
        $recv = reset($recvData)->getPayload();
        $recvData1 = json_decode($recv, true);
        $this->assertTrue(isset($recvData1['clientId']), 'Not found clientId');

        $client2 = new Client($this->host, $this->httpHost);
        $this->assertTrue($client2->connect());
        $this->assertTrue($client2->sendData(json_encode([
            'action'    => 'login',
            'username'  => 'testClose',
        ])));
        $recvData = $client2->receive();
        $recv = reset($recvData)->getPayload();
        $recvData2 = json_decode($recv, true);
        $this->assertTrue($recvData2['success'] ?? null, 'Not found success');

        $http3 = new HttpRequest();
        $response = $http3->post($this->httpHost . 'serverUtil/close', ['clientId' => $recvData1['clientId'], 'flag' => 'testClose']);
        $this->assertEquals([
            'clientId' => 1,
            'flag'     => 1,
        ], $response->json(true));
        $this->assertEquals('', $client1->receive());
        $this->assertEquals('', $client2->receive());
    }
}
