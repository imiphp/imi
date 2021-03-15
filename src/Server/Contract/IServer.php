<?php

declare(strict_types=1);

namespace Imi\Server\Contract;

use Imi\Bean\Container;
use Imi\Event\IEvent;

/**
 * 服务器接口.
 */
interface IServer extends IEvent
{
    /**
     * 获取服务器名称.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * 获取协议名称.
     *
     * @return string
     */
    public function getProtocol(): string;

    /**
     * 获取配置信息.
     *
     * @return array
     */
    public function getConfig(): array;

    /**
     * 获取容器对象
     *
     * @return \Imi\Bean\Container
     */
    public function getContainer(): Container;

    /**
     * 获取Bean对象
     *
     * @param string $name
     * @param mixed  $params
     *
     * @return object
     */
    public function getBean(string $name, ...$params): object;

    /**
     * 是否为长连接服务
     *
     * @return bool
     */
    public function isLongConnection(): bool;

    /**
     * 是否支持 SSL.
     *
     * @return bool
     */
    public function isSSL(): bool;

    /**
     * 开启服务
     *
     * @return void
     */
    public function start();

    /**
     * 终止服务
     *
     * @return void
     */
    public function shutdown();

    /**
     * 重载服务
     *
     * @return void
     */
    public function reload();

    /**
     * 调用服务器方法.
     *
     * @param string $methodName
     * @param mixed  ...$args
     *
     * @return mixed
     */
    public function callServerMethod(string $methodName, ...$args);
}
