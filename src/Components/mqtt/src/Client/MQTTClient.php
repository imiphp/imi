<?php

declare(strict_types=1);

namespace Imi\MQTT\Client;

use BinSoul\Net\Mqtt\Packet;
use BinSoul\Net\Mqtt\Packet\ConnectRequestPacket;
use BinSoul\Net\Mqtt\Packet\ConnectResponsePacket;
use BinSoul\Net\Mqtt\Packet\DisconnectRequestPacket;
use BinSoul\Net\Mqtt\Packet\PingRequestPacket;
use BinSoul\Net\Mqtt\Packet\PublishAckPacket;
use BinSoul\Net\Mqtt\Packet\PublishCompletePacket;
use BinSoul\Net\Mqtt\Packet\PublishReceivedPacket;
use BinSoul\Net\Mqtt\Packet\PublishReleasePacket;
use BinSoul\Net\Mqtt\Packet\PublishRequestPacket;
use BinSoul\Net\Mqtt\Packet\SubscribeRequestPacket;
use BinSoul\Net\Mqtt\Packet\UnsubscribeRequestPacket;
use BinSoul\Net\Mqtt\PacketStream;
use Imi\App;
use Imi\MQTT\Client\Contract\IMQTTClientListener;
use Imi\MQTT\Client\Exception\ConnectException;
use Imi\MQTT\Client\Exception\InvalidPacketTypeException;
use Imi\MQTT\Client\Exception\SendException;
use Swoole\Coroutine\Client;
use Swoole\Timer;

class MQTTClient
{
    /**
     * Swoole 协程客户端.
     *
     * @var \Swoole\Coroutine\Client
     */
    private $client;

    /**
     * 连接信息.
     *
     * @var \Imi\MQTT\Client\Connection
     */
    private $connection;

    /**
     * 事件监听器.
     *
     * @var \Imi\MQTT\Client\Contract\IMQTTClientListener
     */
    private $listener;

    /**
     * 已连接状态
     *
     * @var bool
     */
    private $connected = false;

    /**
     * @var \BinSoul\Net\Mqtt\DefaultPacketFactory
     */
    private $packetFactory;

    /**
     * Ping 定时器ID.
     *
     * @var int
     */
    private $pingTimerId;

    /**
     * 包类型集合.
     */
    public const PACKET_TYPE_MAP = [
        Packet::TYPE_CONNACK    => 'connectACK',
        Packet::TYPE_PUBLISH    => 'publish',
        Packet::TYPE_PUBACK     => 'publishAck',
        Packet::TYPE_PUBREC     => 'publishReceived',
        Packet::TYPE_PUBREL     => 'publishRelease',
        Packet::TYPE_PUBCOMP    => 'publishComplete',
        Packet::TYPE_SUBACK     => 'subscribeACK',
        Packet::TYPE_UNSUBACK   => 'unsubscribeACK',
        Packet::TYPE_PINGRESP   => 'ping',
    ];

    public function __construct(array $config, IMQTTClientListener $listener)
    {
        if (!isset($config['host']))
        {
            throw new \InvalidArgumentException('MQTTClient config must have "host"');
        }
        if (!isset($config['port']))
        {
            throw new \InvalidArgumentException('MQTTClient config must have "port"');
        }
        $this->packetFactory = App::getBean(\BinSoul\Net\Mqtt\DefaultPacketFactory::class);
        $this->listener = $listener;
        if ($configWill = $config['will'] ?? null)
        {
            $will = new Message($configWill['topic'] ?? '', $configWill['payload'] ?? '', $configWill['qosLevel'] ?? 0, $configWill['retain'] ?? false, $configWill['duplicate'] ?? false);
        }
        else
        {
            $will = null;
        }
        $connection = (new Connection($config['host'], $config['port'], $config['timeout'] ?? null, $config['pingTimespan'] ?? null, $config['username'] ?? '', $config['password'] ?? '', $will, $config['clientId'] ?? '', $config['keepAlive'] ?? 60, $config['protocol'] ?? 4, $config['clean'] ?? true))
            ->withSsl($config['ssl'] ?? false)
            ->withSslCertFile($config['sslCertFile'] ?? null)
            ->withSslKeyFile($config['sslKeyFile'] ?? null)
            ->withSslVerifyPeer($config['sslVerifyPeer'] ?? true)
            ->withSslAllowSelfSigned($config['sslAllowSelfSigned'] ?? false)
            ->withSslHostName($config['sslHostName'] ?? null)
            ->withSslCafile($config['sslCafile'] ?? null)
            ->withSslCapath($config['sslCapath'] ?? null)
            ;

        $this->connection = $connection;
        $option = [
            'open_mqtt_protocol'    => true,
        ];
        if ($connection->getSsl())
        {
            $type = \SWOOLE_SOCK_TCP | \SWOOLE_SSL;
            $option['ssl_cert_file'] = $connection->getSslCertFile();
            $option['ssl_key_file'] = $connection->getSslKeyFile();
            $option['ssl_verify_peer'] = $connection->getSslVerifyPeer();
            $option['ssl_allow_self_signed'] = $connection->getSslAllowSelfSigned();
            $option['ssl_host_name'] = $connection->getSslHostName();
            $option['ssl_ca_file'] = $connection->getSslCafile();
            $option['ssl_ca_path'] = $connection->getSslCapath();
        }
        else
        {
            $type = \SWOOLE_SOCK_TCP;
        }
        // Swoole 客户端对象
        $this->client = $client = new Client($type);
        $client->set($option);
    }

    public function __destruct()
    {
        if ($this->pingTimerId)
        {
            Timer::clear($this->pingTimerId);
        }
    }

    /**
     * Get swoole 协程客户端.
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * Get 连接信息.
     *
     * @return \Imi\MQTT\Client\Connection
     */
    public function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * Get 已连接状态
     *
     * @return bool
     */
    public function getConnected()
    {
        return $this->connected;
    }

    /**
     * 连接.
     */
    public function connect(): bool
    {
        $connection = $this->connection;
        // TCP 连接
        if (!$this->client->connect($connection->getHost(), $connection->getPort(), $connection->getTimeout()))
        {
            throw new ConnectException('TCP Connect failed');
        }
        // MQTT 连接
        $connectRequest = new ConnectRequestPacket();
        $connectRequest->setCleanSession($connection->isCleanSession());
        $connectRequest->setClientId($connection->getClientId());
        $connectRequest->setKeepAlive($connection->getKeepAlive());
        $connectRequest->setPassword($connection->getPassword());
        $connectRequest->setProtocolLevel($connection->getProtocol());
        $connectRequest->setUsername($connection->getUsername());
        $will = $connection->getWill();
        if ($will)
        {
            $connectRequest->setWill($will->getTopic(), $will->getPayload(), $will->getQosLevel(), $will->isRetained());
        }
        $this->send($connectRequest);
        $packet = $this->recv();
        if (!$packet instanceof ConnectResponsePacket)
        {
            throw new ConnectException('Send ConnectRequestPacket failed');
        }
        $this->listener->connectACK($this, $packet);
        if ($packet->isError())
        {
            throw new ConnectException('ConnectResponsePacket error: ' . $packet->getErrorName());
        }
        $this->connected = true;

        return true;
    }

    /**
     * 断开连接.
     *
     * @return void
     */
    public function disconnect()
    {
        if ($this->connected)
        {
            $this->send(new DisconnectRequestPacket());
        }
        if ($this->pingTimerId)
        {
            Timer::clear($this->pingTimerId);
        }
        $this->connected = false;
        $this->client->close();
    }

    /**
     * Ping.
     *
     * @return int|bool
     */
    public function ping()
    {
        return $this->send(new PingRequestPacket());
    }

    /**
     * 发布.
     *
     * @return int|bool
     */
    public function publish(string $topic, string $payload, int $qosLevel = 0, bool $duplicate = false, bool $retained = false, ?int $identifier = null)
    {
        $request = new PublishRequestPacket();
        $request->setTopic($topic);
        $request->setPayload($payload);
        $request->setQosLevel($qosLevel);
        $request->setDuplicate($duplicate);
        $request->setRetained($retained);
        $request->setIdentifier($identifier);

        return $this->send($request);
    }

    /**
     * 发布确认.
     *
     * @return int|bool
     */
    public function publishAck(?int $identifier = null)
    {
        $request = new PublishAckPacket();
        $request->setIdentifier($identifier);

        return $this->send($request);
    }

    /**
     * 发布已收到（保证交付部分1）.
     *
     * @return int|bool
     */
    public function publishReceived(?int $identifier = null)
    {
        $request = new PublishReceivedPacket();
        $request->setIdentifier($identifier);

        return $this->send($request);
    }

    /**
     * 发布释放（确保交付的第2部分）.
     *
     * @return int|bool
     */
    public function publishRelease(?int $identifier = null)
    {
        $request = new PublishReleasePacket();
        $request->setIdentifier($identifier);

        return $this->send($request);
    }

    /**
     * 发布完成（保证交付的第3部分）.
     *
     * @return int|bool
     */
    public function publishComplete(?int $identifier = null)
    {
        $request = new PublishCompletePacket();
        $request->setIdentifier($identifier);

        return $this->send($request);
    }

    /**
     * 订阅.
     *
     * @return int|bool
     */
    public function subscribe(string $topic, int $qosLevel = 0, ?int $identifier = null)
    {
        $request = new SubscribeRequestPacket();
        $request->setTopic($topic);
        $request->setQosLevel($qosLevel);
        $request->setIdentifier($identifier);

        return $this->send($request);
    }

    /**
     * 取消订阅.
     *
     * @return int|bool
     */
    public function unsubscribe(array $topics, ?int $identifier = null)
    {
        $request = new UnsubscribeRequestPacket();
        $request->setTopics($topics);
        $request->setIdentifier($identifier);

        return $this->send($request);
    }

    /**
     * 发送包.
     *
     * @return int|bool
     */
    public function send(Packet $packet)
    {
        $stream = new PacketStream();
        $packet->write($stream);
        $client = $this->client;
        $result = $client->send($stream->getData());
        if ($result)
        {
            return $result;
        }
        else
        {
            $class = \get_class($packet);
            $list = explode('\\', $class);
            $classShortName = array_pop($list);
            throw new SendException(sprintf('Send %s failed! error: [%s]%s', $classShortName, $client->errCode, $client->errMsg));
        }
    }

    /**
     * 接收包.
     */
    public function recv(): ?Packet
    {
        $client = $this->client;
        $data = $client->recv();
        if ('' === $data)
        {
            $this->connected = false;
            // 重连
            $this->disconnect();
            $this->connect();

            return null;
        }
        if (!$data)
        {
            throw new SendException(sprintf('Recv failed! error: [%s]%s', $client->errCode, $client->errMsg));
        }
        $type = \ord($data[0]) >> 4;
        $packet = $this->packetFactory->build($type);
        $packet->read(new PacketStream($data));

        return $packet;
    }

    /**
     * 开始循环接收，直到关闭连接.
     *
     * @return void
     */
    public function wait()
    {
        $pingTimespan = $this->connection->getPingTimespan();
        if ($pingTimespan > 0)
        {
            $this->pingTimerId = Timer::tick((int) ($pingTimespan * 1000), [$this, 'ping']);
        }
        while ($this->connected)
        {
            $packet = $this->recv();
            if (null === $packet)
            {
                continue;
            }
            $methodName = self::PACKET_TYPE_MAP[$packet->getPacketType()] ?? null;
            if (null === $methodName)
            {
                throw new InvalidPacketTypeException(sprintf('Unsupport MQTT packet type %s', $packet->getPacketType()));
            }
            $this->listener->$methodName($this, $packet);
        }
        if ($this->pingTimerId)
        {
            Timer::clear($this->pingTimerId);
        }
    }
}
