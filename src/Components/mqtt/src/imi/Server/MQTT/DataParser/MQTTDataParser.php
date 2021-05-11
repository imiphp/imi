<?php

namespace Imi\Server\MQTT\DataParser;

use BinSoul\Net\Mqtt\PacketStream;
use Imi\Aop\Annotation\Inject;
use Imi\Server\DataParser\IParser;
use Imi\Server\MQTT\Exception\InvalidReceiveData;

class MQTTDataParser implements IParser
{
    /**
     * @Inject(\BinSoul\Net\Mqtt\DefaultPacketFactory::class)
     *
     * @var \BinSoul\Net\Mqtt\DefaultPacketFactory
     */
    protected $packetFactory;

    /**
     * 编码为存储格式.
     *
     * @param \BinSoul\Net\Mqtt\Packet $data
     *
     * @return mixed
     */
    public function encode($data)
    {
        $packageStream = new PacketStream();
        $data->write($packageStream);

        return $packageStream->__toString();
    }

    /**
     * 解码为php变量.
     *
     * @param string $data
     *
     * @return \BinSoul\Net\Mqtt\Packet
     */
    public function decode($data)
    {
        if (!isset($data[0]))
        {
            throw new InvalidReceiveData();
        }
        $type = \ord($data[0]) >> 4;
        $packet = $this->packetFactory->build($type);
        $packet->read(new PacketStream($data));

        return $packet;
    }
}
