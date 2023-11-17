<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Test\Driver;

use Imi\ConnectionCenter\Contract\AbstractConnectionDriver;
use Imi\ConnectionCenter\Contract\IConnectionConfig;

class TestDriver extends AbstractConnectionDriver
{
    /**
     * 创建连接配置.
     */
    public static function createConnectionConfig(array $config): IConnectionConfig
    {
        return TestDriverConfig::createFromArray($config);
    }

    /**
     * 创建新连接.
     */
    public function createInstance(): mixed
    {
        $instance = new \stdClass();
        $instance->config = $this->config;
        $instance->connected = false;
        $instance->reseted = true;
        $instance->available = 0;
        $instance->ping = 0;

        return $instance;
    }

    /**
     * 连接.
     *
     * 返回连接对象（也可能是原对象）
     */
    public function connect(mixed $instance): mixed
    {
        $instance->connected = true;

        return $instance;
    }

    /**
     * 关闭.
     */
    public function close(mixed $instance): void
    {
        $instance->connected = false;
    }

    /**
     * 重置资源，当资源被释放后重置一些默认的设置.
     */
    public function reset(mixed $instance): void
    {
        $instance->reseted = true;
    }

    /**
     * 检查是否可用.
     * 此操作是实时检查，能实时返回真实的结果.
     */
    public function checkAvailable(mixed $instance): bool
    {
        ++$instance->available;

        return true;
    }

    /**
     * 发送心跳.
     */
    public function ping(mixed $instance): bool
    {
        ++$instance->ping;

        return true;
    }
}
