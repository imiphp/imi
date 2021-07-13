<?php

declare(strict_types=1);

namespace Imi\Server\MQTT;

use BinSoul\Net\Mqtt\Packet\ConnectRequestPacket;
use BinSoul\Net\Mqtt\Packet\ConnectResponsePacket;
use BinSoul\Net\Mqtt\Packet\DisconnectRequestPacket;
use BinSoul\Net\Mqtt\Packet\PingRequestPacket;
use BinSoul\Net\Mqtt\Packet\PingResponsePacket;
use BinSoul\Net\Mqtt\Packet\PublishRequestPacket;
use BinSoul\Net\Mqtt\Packet\SubscribeRequestPacket;
use BinSoul\Net\Mqtt\Packet\SubscribeResponsePacket;
use BinSoul\Net\Mqtt\Packet\UnsubscribeRequestPacket;
use BinSoul\Net\Mqtt\Packet\UnsubscribeResponsePacket;
use Imi\Server\MQTT\Message\ReceiveData;

/**
 * MQTT 控制器基类.
 */
abstract class BaseMQTTController
{
    /**
     * 连接.
     */
    public function connect(ConnectRequestPacket $request, ReceiveData $receiveData): ?ConnectResponsePacket
    {
        $response = new ConnectResponsePacket();
        $response->setReturnCode(0);

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
    abstract public function publish(PublishRequestPacket $request, ReceiveData $receiveData);

    /**
     * 发布确认.
     */
    public function publishAck(\BinSoul\Net\Mqtt\Packet\PublishAckPacket $request, ReceiveData $receiveData): void
    {
    }

    /**
     * 发布已收到（保证交付部分1）.
     */
    public function publishReceived(\BinSoul\Net\Mqtt\Packet\PublishReceivedPacket $request, ReceiveData $receiveData): void
    {
    }

    /**
     * 发布释放（确保交付的第2部分）.
     */
    public function publishRelease(\BinSoul\Net\Mqtt\Packet\PublishReleasePacket $request, ReceiveData $receiveData): void
    {
    }

    /**
     * 发布完成（保证交付的第3部分）.
     */
    public function publishComplete(\BinSoul\Net\Mqtt\Packet\PublishCompletePacket $request, ReceiveData $receiveData): void
    {
    }

    /**
     * 订阅.
     */
    abstract public function subscribe(SubscribeRequestPacket $request, ReceiveData $receiveData): ?SubscribeResponsePacket;

    /**
     * 取消订阅.
     */
    abstract public function unsubscribe(UnsubscribeRequestPacket $request, ReceiveData $receiveData): ?UnsubscribeResponsePacket;
}
