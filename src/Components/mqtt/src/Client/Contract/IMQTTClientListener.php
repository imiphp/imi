<?php

namespace Imi\MQTT\Client\Contract;

/**
 * MQTT 事件监听接口.
 */
interface IMQTTClientListener
{
    /**
     * 连接确认.
     *
     * @param \Imi\MQTT\Client\MQTTClient                    $client
     * @param \BinSoul\Net\Mqtt\Packet\ConnectResponsePacket $packet
     *
     * @return void
     */
    public function connectACK(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\ConnectResponsePacket $packet);

    /**
     * 发布.
     *
     * @param \Imi\MQTT\Client\MQTTClient                   $client
     * @param \BinSoul\Net\Mqtt\Packet\PublishRequestPacket $packet
     *
     * @return void
     */
    public function publish(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\PublishRequestPacket $packet);

    /**
     * 发布确认.
     *
     * @param \Imi\MQTT\Client\MQTTClient               $client
     * @param \BinSoul\Net\Mqtt\Packet\PublishAckPacket $packet
     *
     * @return void
     */
    public function publishAck(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\PublishAckPacket $packet);

    /**
     * 发布已收到（保证交付部分1）.
     *
     * @param \Imi\MQTT\Client\MQTTClient                    $client
     * @param \BinSoul\Net\Mqtt\Packet\PublishReceivedPacket $packet
     *
     * @return void
     */
    public function publishReceived(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\PublishReceivedPacket $packet);

    /**
     * 发布释放（确保交付的第2部分）.
     *
     * @param \Imi\MQTT\Client\MQTTClient                   $client
     * @param \BinSoul\Net\Mqtt\Packet\PublishReleasePacket $packet
     *
     * @return void
     */
    public function publishRelease(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\PublishReleasePacket $packet);

    /**
     * 发布完成（保证交付的第3部分）.
     *
     * @param \Imi\MQTT\Client\MQTTClient                    $client
     * @param \BinSoul\Net\Mqtt\Packet\PublishCompletePacket $packet
     *
     * @return void
     */
    public function publishComplete(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\PublishCompletePacket $packet);

    /**
     * 订阅确认.
     *
     * @param \Imi\MQTT\Client\MQTTClient                      $client
     * @param \BinSoul\Net\Mqtt\Packet\SubscribeResponsePacket $packet
     *
     * @return void
     */
    public function subscribeACK(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\SubscribeResponsePacket $packet);

    /**
     * 取消订阅确认.
     *
     * @param \Imi\MQTT\Client\MQTTClient                        $client
     * @param \BinSoul\Net\Mqtt\Packet\UnsubscribeResponsePacket $packet
     *
     * @return void
     */
    public function unsubscribeACK(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\UnsubscribeResponsePacket $packet);

    /**
     * Ping 响应.
     *
     * @param \Imi\MQTT\Client\MQTTClient                 $client
     * @param \BinSoul\Net\Mqtt\Packet\PingResponsePacket $packet
     *
     * @return void
     */
    public function ping(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\PingResponsePacket $packet);
}
