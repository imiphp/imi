<?php

declare(strict_types=1);

namespace Imi\Util\Socket;

/**
 * IP 端点.
 */
class IPEndPoint implements \Stringable
{
    /**
     * 地址
     */
    private string $address;

    /**
     * 端口.
     */
    private int $port;

    public function __construct(string $address, int $port)
    {
        $this->address = $address;
        $this->port = $port;
    }

    public function __toString(): string
    {
        return $this->address . ':' . $this->port;
    }

    /**
     * 获取地址
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * 获取端口.
     */
    public function getPort(): int
    {
        return $this->port;
    }
}
