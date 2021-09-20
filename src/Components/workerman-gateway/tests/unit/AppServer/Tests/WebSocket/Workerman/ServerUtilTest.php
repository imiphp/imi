<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Test\AppServer\Tests\WebSocket\Workerman;

use Imi\WorkermanGateway\Test\AppServer\Tests\WebSocket\BaseTest;
use Wrench\Client;
use Yurun\Util\HttpRequest;

/**
 * @testdox Imi\WorkermanGateway\Server\Server
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
        $client1 = $client2 = $client3 = null;
        $this->go(function () use (&$client1, &$client2, &$client3) {
            $client1 = new Client($this->host, $this->httpHost);
            $this->assertTrue($client1->connect());
            sleep(1);
            $this->assertTrue($client1->sendData(json_encode([
                'action'    => 'info',
            ])));
            $recvData1 = $client1->receive();
            $recv = reset($recvData1)->getPayload();
            $recvData1 = json_decode($recv, true);
            $this->assertTrue(isset($recvData1['clientId']));

            $client2 = new Client($this->host, $this->httpHost);
            $this->assertTrue($client2->connect());
            sleep(1);
            $this->assertTrue($client2->sendData(json_encode([
                'action'    => 'info',
            ])));
            $recvData2 = $client2->receive();
            $recv = reset($recvData2)->getPayload();
            $recvData2 = json_decode($recv, true);
            $this->assertTrue(isset($recvData2['clientId']));

            $client3 = new Client($this->host, $this->httpHost);
            $this->assertTrue($client3->connect());
            sleep(1);
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
                'send2'         => 1,
                'send3'         => 1,
                'sendByFlag'    => 1,
                'sendRaw2'      => 1,
                'sendRaw3'      => 1,
                'sendRawByFlag' => 1,
                'sendToAll'     => 1,
                'sendRawToAll'  => 1,
            ], $response->json(true));

            $expectedData = json_encode(['data' => 'test']);
            $count = 0;
            for ($i = 0; $i < 6 && $count < 6; ++$i)
            {
                $this->assertIsArray($recvDatas = $client1->receive());
                foreach ($recvDatas as $recvData)
                {
                    $recv = $recvData->getPayload();
                    if ($expectedData === $recv)
                    {
                        ++$count;
                    }
                }
            }
            $this->assertEquals(6, $count);

            $count = 0;
            for ($i = 0; $i < 4 && $count < 4; ++$i)
            {
                $this->assertIsArray($recvDatas = $client2->receive());
                foreach ($recvDatas as $recvData)
                {
                    $recv = $recvData->getPayload();
                    if ($expectedData === $recv)
                    {
                        ++$count;
                    }
                }
            }
            $this->assertEquals(4, $count);

            $count = 0;
            for ($i = 0; $i < 4 && $count < 4; ++$i)
            {
                $this->assertIsArray($recvDatas = $client3->receive());
                foreach ($recvDatas as $recvData)
                {
                    $recv = $recvData->getPayload();
                    if ($expectedData === $recv)
                    {
                        ++$count;
                    }
                }
            }
            $this->assertEquals(4, $count);
        }, function () use (&$client1, &$client2, &$client3) {
            if ($client1)
            {
                $client1->disconnect();
            }
            if ($client2)
            {
                $client2->disconnect();
            }
            if ($client3)
            {
                $client3->disconnect();
            }
        });
    }

    public function testSendToGroup(): void
    {
        /** @var Client[] $clients */
        $clients = [];
        $this->go(function () use (&$clients) {
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

            $expectedData = json_encode(['data' => 'test']);
            foreach ($clients as $client)
            {
                $count = 0;
                for ($i = 0; $i < 2 && $count < 2; ++$i)
                {
                    $this->assertIsArray($recvDatas = $client->receive());
                    foreach ($recvDatas as $recvData)
                    {
                        $recv = $recvData->getPayload();
                        if ($expectedData === $recv)
                        {
                            ++$count;
                        }
                    }
                }
                $this->assertEquals(2, $count);
            }
        }, function () use (&$clients) {
            foreach ($clients as $client)
            {
                $client->disconnect();
            }
        });
    }

    public function testExists(): void
    {
        $client1 = $client2 = null;
        try
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
        }
        finally
        {
            if ($client1)
            {
                $client1->disconnect();
            }
            if ($client2)
            {
                $client2->disconnect();
            }
        }
    }

    public function testClose(): void
    {
        $client1 = $client2 = null;
        try
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
        catch (\Throwable $th)
        {
            if ($client1)
            {
                $client1->disconnect();
            }
            if ($client2)
            {
                $client2->disconnect();
            }
            throw $th;
        }
    }
}
