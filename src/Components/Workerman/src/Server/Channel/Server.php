<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Channel;

use Imi\Bean\Annotation\Bean;
use ReflectionClass;
use Workerman\Worker;

/**
 * @Bean("WorkermanChannelServer")
 */
class Server extends \Imi\Workerman\Server\Tcp\Server
{
    /**
     * 通道服务器.
     */
    protected \Channel\Server $channelServer;

    protected function createServer(): Worker
    {
        $config = $this->config;
        if (isset($config['socketName']))
        {
            $ip = $config['socketName'] ?? '0.0.0.0';
            $port = 2206;
        }
        else
        {
            $ip = $config['host'] ?? '0.0.0.0';
            $port = $config['port'] ?? 2206;
        }
        $channelServer = $this->channelServer = new \Channel\Server($ip, $port);
        $refClass = new ReflectionClass($channelServer);
        $property = $refClass->getProperty('_worker');
        $property->setAccessible(true);
        $worker = $this->worker = $property->getValue($channelServer);
        foreach ($config['configs'] as $k => $v)
        {
            $worker->$k = $v;
        }

        return $worker;
    }

    /**
     * 绑定服务器事件.
     */
    protected function bindEvents(): void
    {
    }
}
