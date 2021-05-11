<?php

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
     *
     * @param \BinSoul\Net\Mqtt\Packet\ConnectRequestPacket $request
     * @param \Imi\Server\MQTT\Message\ReceiveData          $receiveData
     *
     * @return \BinSoul\Net\Mqtt\Packet\ConnectResponsePacket|null
     */
    public function connect(ConnectRequestPacket $request, ReceiveData $receiveData): ?ConnectResponsePacket
    {
        $response = new ConnectResponsePacket();
        $response->setReturnCode(0);

        return $response;
    }

    /**
     * 断开连接.
     *
     * @param \BinSoul\Net\Mqtt\Packet\DisconnectRequestPacket $request
     * @param \Imi\Server\MQTT\Message\ReceiveData             $receiveData
     *
     * @return void
     */
    public function disconnect(DisconnectRequestPacket $request, ReceiveData $receiveData): void
    {
    }

    /**
     * Ping.
     *
     * @param \BinSoul\Net\Mqtt\Packet\PingRequestPacket $request
     * @param \Imi\Server\MQTT\Message\ReceiveData       $receiveData
     *
     * @return \BinSoul\Net\Mqtt\Packet\PingResponsePacket|null
     */
    public function ping(PingRequestPacket $request, ReceiveData $receiveData): ?PingResponsePacket
    {
        return new PingResponsePacket();
    }

    /**
     * 发布.
     *
     * @param \BinSoul\Net\Mqtt\Packet\PublishRequestPacket $request
     * @param \Imi\Server\MQTT\Message\ReceiveData          $receiveData
     *
     * @return \BinSoul\Net\Mqtt\Packet\PublishAckPacket|\BinSoul\Net\Mqtt\Packet\PublishReceivedPacket|\BinSoul\Net\Mqtt\Packet\PublishReleasePacket|\BinSoul\Net\Mqtt\Packet\PublishCompletePacket|null
     */
    abstract public function publish(PublishRequestPacket $request, ReceiveData $receiveData);

    /**
     * 发布确认.
     *
     * @param \BinSoul\Net\Mqtt\Packet\PublishAckPacket $request
     * @param \Imi\Server\MQTT\Message\ReceiveData      $receiveData
     *
     * @return void
     */
    public function publishAck(\BinSoul\Net\Mqtt\Packet\PublishAckPacket $request, ReceiveData $receiveData)
    {
    }

    /**
     * 发布已收到（保证交付部分1）.
     *
     * @param \BinSoul\Net\Mqtt\Packet\PublishReceivedPacket $request
     * @param \Imi\Server\MQTT\Message\ReceiveData           $receiveData
     *
     * @return void
     */
    public function publishReceived(\BinSoul\Net\Mqtt\Packet\PublishReceivedPacket $request, ReceiveData $receiveData)
    {
    }

    /**
     * 发布释放（确保交付的第2部分）.
     *
     * @param \BinSoul\Net\Mqtt\Packet\PublishReleasePacket $request
     * @param \Imi\Server\MQTT\Message\ReceiveData          $receiveData
     *
     * @return void
     */
    public function publishRelease(\BinSoul\Net\Mqtt\Packet\PublishReleasePacket $request, ReceiveData $receiveData)
    {
    }

    /**
     * 发布完成（保证交付的第3部分）.
     *
     * @param \BinSoul\Net\Mqtt\Packet\PublishCompletePacket $request
     * @param \Imi\Server\MQTT\Message\ReceiveData           $receiveData
     *
     * @return void
     */
    public function publishComplete(\BinSoul\Net\Mqtt\Packet\PublishCompletePacket $request, ReceiveData $receiveData)
    {
    }

    /**
     * 订阅.
     *
     * @param \BinSoul\Net\Mqtt\Packet\SubscribeRequestPacket $request
     * @param \Imi\Server\MQTT\Message\ReceiveData            $receiveData
     *
     * @return \BinSoul\Net\Mqtt\Packet\SubscribeResponsePacket|null
     */
    abstract public function subscribe(SubscribeRequestPacket $request, ReceiveData $receiveData): ?SubscribeResponsePacket;

    /**
     * 取消订阅.
     *
     * @param \BinSoul\Net\Mqtt\Packet\UnsubscribeRequestPacket $request
     * @param \Imi\Server\MQTT\Message\ReceiveData              $receiveData
     *
     * @return \BinSoul\Net\Mqtt\Packet\UnsubscribeResponsePacket|null
     */
    abstract public function unsubscribe(UnsubscribeRequestPacket $request, ReceiveData $receiveData): ?UnsubscribeResponsePacket;
}
