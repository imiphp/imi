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
     */
    public function connectACK(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\ConnectResponsePacket $packet): void;

    /**
     * 发布.
     */
    public function publish(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\PublishRequestPacket $packet): void;

    /**
     * 发布确认.
     */
    public function publishAck(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\PublishAckPacket $packet): void;

    /**
     * 发布已收到（保证交付部分1）.
     */
    public function publishReceived(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\PublishReceivedPacket $packet): void;

    /**
     * 发布释放（确保交付的第2部分）.
     */
    public function publishRelease(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\PublishReleasePacket $packet): void;

    /**
     * 发布完成（保证交付的第3部分）.
     */
    public function publishComplete(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\PublishCompletePacket $packet): void;

    /**
     * 订阅确认.
     */
    public function subscribeACK(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\SubscribeResponsePacket $packet): void;

    /**
     * 取消订阅确认.
     */
    public function unsubscribeACK(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\UnsubscribeResponsePacket $packet): void;

    /**
     * Ping 响应.
     */
    public function ping(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\PingResponsePacket $packet): void;
}
