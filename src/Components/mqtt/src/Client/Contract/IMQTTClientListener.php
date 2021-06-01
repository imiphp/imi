<?php

declare(strict_types=1);

namespace Imi\MQTT\Client\Contract;

/**
 * MQTT 事件监听接口.
 */
interface IMQTTClientListener
{
    /**
     * 连接确认.
     *
     * @return void
     */
    public function connectACK(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\ConnectResponsePacket $packet);

    /**
     * 发布.
     *
     * @return void
     */
    public function publish(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\PublishRequestPacket $packet);

    /**
     * 发布确认.
     *
     * @return void
     */
    public function publishAck(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\PublishAckPacket $packet);

    /**
     * 发布已收到（保证交付部分1）.
     *
     * @return void
     */
    public function publishReceived(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\PublishReceivedPacket $packet);

    /**
     * 发布释放（确保交付的第2部分）.
     *
     * @return void
     */
    public function publishRelease(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\PublishReleasePacket $packet);

    /**
     * 发布完成（保证交付的第3部分）.
     *
     * @return void
     */
    public function publishComplete(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\PublishCompletePacket $packet);

    /**
     * 订阅确认.
     *
     * @return void
     */
    public function subscribeACK(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\SubscribeResponsePacket $packet);

    /**
     * 取消订阅确认.
     *
     * @return void
     */
    public function unsubscribeACK(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\UnsubscribeResponsePacket $packet);

    /**
     * Ping 响应.
     *
     * @return void
     */
    public function ping(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\PingResponsePacket $packet);
}
