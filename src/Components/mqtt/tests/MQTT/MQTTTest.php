<?php

declare(strict_types=1);

namespace Imi\MQTT\Test;

use BinSoul\Net\Mqtt\Packet;
use Imi\MQTT\Client\MQTTClient;
use Swoole\Coroutine;
use Swoole\Coroutine\Channel;

class MQTTTest extends BaseTest
{
    public function testMQTT(): void
    {
        $this->privateTest([
            'host'          => '127.0.0.1',
            'port'          => 8081,
            'timeout'       => 60,
            'pingTimespan'  => 3,
            'username'      => 'root',
            'password'      => '123456',
        ]);
    }

    public function testMQTTS(): void
    {
        $this->privateTest([
            'host'                  => '127.0.0.1',
            'port'                  => 8082,
            'timeout'               => 60,
            'pingTimespan'          => 3,
            'username'              => 'root',
            'password'              => '123456',
            'ssl'                   => true,
            'sslAllowSelfSigned'    => true,
        ]);
    }

    private function privateTest(array $config): void
    {
        $listener = new TestClientListener();
        $initChannel = new Channel(1);
        $client = new MQTTClient($config, $listener);
        Coroutine::create(function () use ($initChannel, $client) {
            $this->assertTrue($client->connect());
            $initChannel->push(1);
            $client->wait();
            $initChannel->push(2);
        });
        $this->assertEquals(1, $initChannel->pop(5));
        $this->assertNotFalse($client->ping());
        $this->assertNotFalse($client->publish('a', 'a'));
        $this->assertNotFalse($client->publish('b', 'b'));
        $this->assertNotFalse($client->publish('c', 'c'));
        $this->assertNotFalse($client->publish('d', 'd'));
        $this->assertNotFalse($client->subscribe('a', 0));
        $this->assertNotFalse($client->unsubscribe(['a']));
        $this->assertEquals(2, $initChannel->pop(5));

        // connectACK
        $r = $listener->getConnectACKResult();
        $this->assertNotNull($r);
        $this->assertTrue($r->isSuccess());

        // publish
        $r = $listener->getPublishResults();
        $this->assertNotNull($r);
        $result = [
            Packet::TYPE_PUBLISH    => false,
            Packet::TYPE_PUBACK     => false,
            Packet::TYPE_PUBREC     => false,
            Packet::TYPE_PUBREL     => false,
            Packet::TYPE_PUBCOMP    => false,
        ];
        foreach ($r as $item)
        {
            /* @var \BinSoul\Net\Mqtt\Packet\IdentifierOnlyPacket $item */
            $result[$item->getPacketType()] = true;
        }
        $this->assertEquals([
            Packet::TYPE_PUBLISH    => true,
            Packet::TYPE_PUBACK     => true,
            Packet::TYPE_PUBREC     => true,
            Packet::TYPE_PUBREL     => true,
            Packet::TYPE_PUBCOMP    => true,
        ], $result);

        // subscribeACK
        $r = $listener->getSubscribeACKResult();
        $this->assertNotNull($r);
        $this->assertEquals([0], $r->getReturnCodes());

        // unsubscribeACK
        $r = $listener->getUnsubscribeACKResult();
        $this->assertNotNull($r);

        // ping
        $r = $listener->getPingResult();
        $this->assertNotNull($r);
    }
}
