<?php

namespace Imi\MQTT\Test;

use Imi\MQTT\Client\Contract\IMQTTClientListener;

class TestClientListener implements IMQTTClientListener
{
    /**
     * @var \BinSoul\Net\Mqtt\Packet\ConnectResponsePacket
     */
    private $connectACKResult;

    /**
     * @var array
     */
    private $publishResults;

    /**
     * @var \BinSoul\Net\Mqtt\Packet\SubscribeResponsePacket
     */
    private $subscribeACKResult;

    /**
     * @var \BinSoul\Net\Mqtt\Packet\UnsubscribeResponsePacket
     */
    private $unsubscribeACKResult;

    /**
     * @var \BinSoul\Net\Mqtt\Packet\PingResponsePacket
     */
    private $pingResult;

    /**
     * 连接确认.
     *
     * @param \Imi\MQTT\Client\MQTTClient                    $client
     * @param \BinSoul\Net\Mqtt\Packet\ConnectResponsePacket $packet
     *
     * @return void
     */
    public function connectACK(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\ConnectResponsePacket $packet)
    {
        $this->connectACKResult = $packet;
    }

    /**
     * 发布.
     *
     * @param \Imi\MQTT\Client\MQTTClient                   $client
     * @param \BinSoul\Net\Mqtt\Packet\PublishRequestPacket $packet
     *
     * @return void
     */
    public function publish(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\PublishRequestPacket $packet)
    {
        $this->publishResults[$packet->getPacketType()] = $packet;
    }

    /**
     * 发布确认.
     *
     * @param \Imi\MQTT\Client\MQTTClient               $client
     * @param \BinSoul\Net\Mqtt\Packet\PublishAckPacket $packet
     *
     * @return void
     */
    public function publishAck(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\PublishAckPacket $packet)
    {
        $this->publishResults[$packet->getPacketType()] = $packet;
    }

    /**
     * 发布已收到（保证交付部分1）.
     *
     * @param \Imi\MQTT\Client\MQTTClient                    $client
     * @param \BinSoul\Net\Mqtt\Packet\PublishReceivedPacket $packet
     *
     * @return void
     */
    public function publishReceived(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\PublishReceivedPacket $packet)
    {
        $this->publishResults[$packet->getPacketType()] = $packet;
    }

    /**
     * 发布释放（确保交付的第2部分）.
     *
     * @param \Imi\MQTT\Client\MQTTClient                   $client
     * @param \BinSoul\Net\Mqtt\Packet\PublishReleasePacket $packet
     *
     * @return void
     */
    public function publishRelease(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\PublishReleasePacket $packet)
    {
        $this->publishResults[$packet->getPacketType()] = $packet;
    }

    /**
     * 发布完成（保证交付的第3部分）.
     *
     * @param \Imi\MQTT\Client\MQTTClient                    $client
     * @param \BinSoul\Net\Mqtt\Packet\PublishCompletePacket $packet
     *
     * @return void
     */
    public function publishComplete(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\PublishCompletePacket $packet)
    {
        $this->publishResults[$packet->getPacketType()] = $packet;
    }

    /**
     * 订阅确认.
     *
     * @param \Imi\MQTT\Client\MQTTClient                      $client
     * @param \BinSoul\Net\Mqtt\Packet\SubscribeResponsePacket $packet
     *
     * @return void
     */
    public function subscribeACK(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\SubscribeResponsePacket $packet)
    {
        $this->subscribeACKResult = $packet;
    }

    /**
     * 取消订阅确认.
     *
     * @param \Imi\MQTT\Client\MQTTClient                        $client
     * @param \BinSoul\Net\Mqtt\Packet\UnsubscribeResponsePacket $packet
     *
     * @return void
     */
    public function unsubscribeACK(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\UnsubscribeResponsePacket $packet)
    {
        $this->unsubscribeACKResult = $packet;
        $client->disconnect();
    }

    /**
     * Ping 响应.
     *
     * @param \Imi\MQTT\Client\MQTTClient                 $client
     * @param \BinSoul\Net\Mqtt\Packet\PingResponsePacket $packet
     *
     * @return void
     */
    public function ping(\Imi\MQTT\Client\MQTTClient $client, \BinSoul\Net\Mqtt\Packet\PingResponsePacket $packet)
    {
        $this->pingResult = $packet;
    }

    /**
     * Get the value of connectACKResult.
     *
     * @return \BinSoul\Net\Mqtt\Packet\ConnectResponsePacket
     */
    public function getConnectACKResult()
    {
        return $this->connectACKResult;
    }

    /**
     * Get the value of subscribeACKResult.
     *
     * @return \BinSoul\Net\Mqtt\Packet\SubscribeResponsePacket
     */
    public function getSubscribeACKResult()
    {
        return $this->subscribeACKResult;
    }

    /**
     * Get the value of unsubscribeACKResult.
     *
     * @return \BinSoul\Net\Mqtt\Packet\UnsubscribeResponsePacket
     */
    public function getUnsubscribeACKResult()
    {
        return $this->unsubscribeACKResult;
    }

    /**
     * Get the value of pingResult.
     *
     * @return \BinSoul\Net\Mqtt\Packet\PingResponsePacket
     */
    public function getPingResult()
    {
        return $this->pingResult;
    }

    /**
     * Get the value of publishResults.
     *
     * @return array
     */
    public function getPublishResults()
    {
        return $this->publishResults;
    }
}
