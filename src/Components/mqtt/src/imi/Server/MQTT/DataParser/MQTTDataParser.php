<?php

declare(strict_types=1);

namespace Imi\Server\MQTT\DataParser;

use BinSoul\Net\Mqtt\DefaultPacketFactory;
use BinSoul\Net\Mqtt\PacketStream;
use Imi\Aop\Annotation\Inject;
use Imi\Bean\Annotation\Bean;
use Imi\Server\DataParser\IParser;
use Imi\Server\MQTT\Exception\InvalidReceiveData;

#[Bean(recursion: false)]
class MQTTDataParser implements IParser
{
    #[Inject(name: \BinSoul\Net\Mqtt\DefaultPacketFactory::class)]
    protected DefaultPacketFactory $packetFactory;

    /**
     * 编码为存储格式.
     *
     * @param \BinSoul\Net\Mqtt\Packet $data
     */
    public function encode(mixed $data): string
    {
        $packageStream = new PacketStream();
        $data->write($packageStream);

        return $packageStream->__toString();
    }

    /**
     * 解码为php变量.
     */
    public function decode(string $data): \BinSoul\Net\Mqtt\Packet
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
