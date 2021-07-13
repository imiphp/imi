<?php

declare(strict_types=1);

namespace Imi\SwooleTracker;

abstract class BaseMiddleware
{
    /**
     * 服务名.
     */
    protected string $serviceName = 'imi';

    /**
     * 服务器 IP，默认获取当前网卡 IP.
     */
    protected ?string $serverIp = null;

    /**
     * 网卡 interface 名
     * 自动获取当前网卡IP时有效.
     */
    protected ?string $interface = null;

    /**
     * 当成功时上报的默认code.
     */
    protected int $successCode = 0;

    /**
     * 当发生异常时上报的默认code.
     */
    protected int $exceptionCode = 500;

    public function __init(): void
    {
        if (null === $this->serverIp)
        {
            $this->serverIp = $this->getLocalIP();
        }
    }

    /**
     * 获取本机IP.
     *
     * @return string
     */
    protected function getLocalIP()
    {
        $list = swoole_get_local_ip();
        if ($this->interface)
        {
            return $list[$this->interface] ?? null;
        }
        if (!$this->serverIp)
        {
            return current($list);
        }

        return '';
    }
}
