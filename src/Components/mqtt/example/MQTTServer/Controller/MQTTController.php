<?php

declare(strict_types=1);

namespace MQTTApp\MQTTServer\Controller;

use BinSoul\Net\Mqtt\Packet\ConnectRequestPacket;
use BinSoul\Net\Mqtt\Packet\ConnectResponsePacket;
use BinSoul\Net\Mqtt\Packet\DisconnectRequestPacket;
use BinSoul\Net\Mqtt\Packet\PingRequestPacket;
use BinSoul\Net\Mqtt\Packet\PingResponsePacket;
use BinSoul\Net\Mqtt\Packet\PublishAckPacket;
use BinSoul\Net\Mqtt\Packet\PublishCompletePacket;
use BinSoul\Net\Mqtt\Packet\PublishReceivedPacket;
use BinSoul\Net\Mqtt\Packet\PublishReleasePacket;
use BinSoul\Net\Mqtt\Packet\PublishRequestPacket;
use BinSoul\Net\Mqtt\Packet\SubscribeRequestPacket;
use BinSoul\Net\Mqtt\Packet\SubscribeResponsePacket;
use BinSoul\Net\Mqtt\Packet\UnsubscribeRequestPacket;
use BinSoul\Net\Mqtt\Packet\UnsubscribeResponsePacket;
use Imi\Server\MQTT\BaseMQTTController;
use Imi\Server\MQTT\Message\ReceiveData;
use Imi\Server\Server;

class MQTTController extends BaseMQTTController
{
    /**
     * 连接.
     */
    public function connect(ConnectRequestPacket $request, ReceiveData $receiveData): ?ConnectResponsePacket
    {
        $success = 'root' === $request->getUsername() && '123456' === $request->getPassword();
        $response = new ConnectResponsePacket();
        if ($success)
        {
            $response->setReturnCode(0);
        }
        else
        {
            $response->setReturnCode(4);
        }

        return $response;
    }

    /**
     * 断开连接.
     */
    public function disconnect(DisconnectRequestPacket $request, ReceiveData $receiveData): void
    {
    }

    /**
     * Ping.
     */
    public function ping(PingRequestPacket $request, ReceiveData $receiveData): ?PingResponsePacket
    {
        return new PingResponsePacket();
    }

    /**
     * 发布.
     *
     * @return \BinSoul\Net\Mqtt\Packet\PublishAckPacket|\BinSoul\Net\Mqtt\Packet\PublishReceivedPacket|\BinSoul\Net\Mqtt\Packet\PublishReleasePacket|\BinSoul\Net\Mqtt\Packet\PublishCompletePacket|null
     */
    public function publish(PublishRequestPacket $request, ReceiveData $receiveData)
    {
        switch ($request->getTopic())
        {
            case 'a':
                $response = new PublishAckPacket();
                break;
            case 'b':
                $response = new PublishReceivedPacket();
                break;
            case 'c':
                $response = new PublishReleasePacket();
                break;
            case 'd':
                $response = new PublishCompletePacket();
                break;
            default:
                throw new \RuntimeException(sprintf('Invalid topic %s', $request->getTopic()));
        }
        $response->setIdentifier($request->getIdentifier());

        return $response;
    }

    /**
     * 订阅.
     */
    public function subscribe(SubscribeRequestPacket $request, ReceiveData $receiveData): ?SubscribeResponsePacket
    {
        $response = new SubscribeResponsePacket();
        $response->setIdentifier($request->getIdentifier());
        $response->setReturnCodes([0]);

        $publishData = new PublishRequestPacket();
        $publishData->setPayload('test');
        $publishData->setTopic('a');
        Server::send($publishData, $receiveData->getClientId());

        return $response;
    }

    /**
     * 取消订阅.
     */
    public function unsubscribe(UnsubscribeRequestPacket $request, ReceiveData $receiveData): ?UnsubscribeResponsePacket
    {
        $response = new UnsubscribeResponsePacket();
        $response->setIdentifier($request->getIdentifier());

        return $response;
    }
}
