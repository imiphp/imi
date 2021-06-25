<?php

declare(strict_types=1);

namespace Imi\Server\Contract;

use Imi\Bean\Container;
use Imi\Event\IEvent;
use Imi\Util\Socket\IPEndPoint;

/**
 * 服务器接口.
 */
interface IServer extends IEvent
{
    /**
     * 获取服务器名称.
     */
    public function getName(): string;

    /**
     * 获取协议名称.
     */
    public function getProtocol(): string;

    /**
     * 获取配置信息.
     */
    public function getConfig(): array;

    /**
     * 获取容器对象
     */
    public function getContainer(): Container;

    /**
     * 获取Bean对象
     *
     * @param mixed $params
     */
    public function getBean(string $name, ...$params): object;

    /**
     * 是否为长连接服务
     */
    public function isLongConnection(): bool;

    /**
     * 是否支持 SSL.
     */
    public function isSSL(): bool;

    /**
     * 开启服务
     */
    public function start(): void;

    /**
     * 终止服务
     */
    public function shutdown(): void;

    /**
     * 重载服务
     */
    public function reload(): void;

    /**
     * 调用服务器方法.
     *
     * @param mixed ...$args
     *
     * @return mixed
     */
    public function callServerMethod(string $methodName, ...$args);

    /**
     * 获取客户端地址
     *
     * @param string|int $clientId
     */
    public function getClientAddress($clientId): IPEndPoint;
}
