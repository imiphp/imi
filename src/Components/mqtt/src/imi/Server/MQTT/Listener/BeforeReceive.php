<?php

declare(strict_types=1);

namespace Imi\Server\MQTT\Listener;

use BinSoul\Net\Mqtt\Packet;
use Imi\Bean\Annotation\ClassEventListener;
use Imi\RequestContext;
use Imi\Server\DataParser\DataParser;
use Imi\Server\MQTT\Message\ReceiveData;
use Imi\Swoole\Server\Event\Param\ReceiveEventParam;
use Imi\Swoole\SwooleWorker;

/**
 * Receive事件前置处理.
 *
 * @ClassEventListener(className="Imi\Server\MQTT\Server",eventName="receive",priority=Imi\Util\ImiPriority::IMI_MAX)
 */
class BeforeReceive extends \Imi\Swoole\Server\TcpServer\Listener\BeforeReceive
{
    /**
     * 包类型集合.
     */
    public const PACKET_TYPE_MAP = [
        Packet::TYPE_CONNECT        => 'connect',
        Packet::TYPE_DISCONNECT     => 'disconnect',
        Packet::TYPE_PINGREQ        => 'ping',
        Packet::TYPE_PUBLISH        => 'publish',
        Packet::TYPE_PUBACK         => 'publishAck',
        Packet::TYPE_PUBREC         => 'publishReceived',
        Packet::TYPE_PUBREL         => 'publishRelease',
        Packet::TYPE_PUBCOMP        => 'publishComplete',
        Packet::TYPE_SUBSCRIBE      => 'subscribe',
        Packet::TYPE_UNSUBSCRIBE    => 'unsubscribe',
    ];

    /**
     * {@inheritDoc}
     */
    public function handle(ReceiveEventParam $e): void
    {
        $clientId = $e->clientId;
        $server = $e->server;
        if (!SwooleWorker::isWorkerStartAppComplete())
        {
            $server->getSwooleServer()->close($clientId);
            $e->stopPropagation();

            return;
        }

        $controller = $server->getConfig()['controller'] ?? null;
        if (!$controller)
        {
            throw new \RuntimeException('MQTT server config "controller" not found');
        }

        // 上下文创建
        $requestContext = RequestContext::getContext();
        $requestContext['server'] = $server;
        $requestContext['clientId'] = $clientId;

        // 数据
        $data = new ReceiveData($clientId, $e->reactorId, $e->data);
        $requestContext['receiveData'] = $data;

        /** @var \Imi\Server\MQTT\BaseMQTTController $controllerInstance */
        $controllerInstance = $server->getBean($controller);
        $packet = $data->getFormatData();
        $methodName = self::PACKET_TYPE_MAP[$packet->getPacketType()] ?? null;
        if (null === $methodName)
        {
            throw new \RuntimeException(sprintf('Unsupport MQTT packet type %s', $packet->getPacketType()));
        }
        $response = $controllerInstance->$methodName($packet, $data);
        if ($response)
        {
            $server->getSwooleServer()->send($clientId, $server->getBean(DataParser::class)->encode($response));
        }
    }
}
