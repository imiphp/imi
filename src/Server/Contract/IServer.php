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
}
