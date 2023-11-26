<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Test\Driver;

use Imi\ConnectionCenter\Contract\AbstractConnectionDriver;
use Imi\ConnectionCenter\Contract\IConnectionConfig;

class TestDriver extends AbstractConnectionDriver
{
    public static function createConnectionConfig(string|array $config): IConnectionConfig
    {
        return TestDriverConfig::create($config);
    }

    protected function createInstanceByConfig(IConnectionConfig $config): object
    {
        $instance = new \stdClass();
        $instance->config = $config;
        $instance->connected = false;
        $instance->reseted = false;
        $instance->available = 0;
        $instance->ping = 0;
        usleep(1000); // 没有IO操作，防止执行过快，统计不到时长

        return $instance;
    }

    public function connect(object $instance): object
    {
        $instance->connected = true;

        return $instance;
    }

    public function close(object $instance): void
    {
        $instance->connected = false;
    }

    public function reset(object $instance): void
    {
        $instance->reseted = true;
    }

    public function checkAvailable(object $instance): bool
    {
        ++$instance->available;

        return $instance->connected;
    }

    public function ping(object $instance): bool
    {
        ++$instance->ping;

        return $instance->pingResult ?? true;
    }
}
