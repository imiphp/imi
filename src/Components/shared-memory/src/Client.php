<?php

declare(strict_types=1);

namespace Imi\SharedMemory;

class Client
{
    /**
     * 客户端对象
     */
    private ?\Yurun\Swoole\SharedMemory\Client\Client $client = null;

    /**
     * 存储器数组.
     */
    private array $objects = [];

    public function __construct(array $options = [])
    {
        $this->client = new \Yurun\Swoole\SharedMemory\Client\Client($options);
        foreach ($options['storeTypes'] as $k => $v)
        {
            if (is_numeric($k))
            {
                $refClass = new \ReflectionClass($v);
                $this->objects[$refClass->getShortName()] = new $v($this->client);
            }
            else
            {
                $this->objects[$k] = new $v($this->client);
            }
        }
    }

    /**
     * 获取客户端对象
     */
    public function getClient(): \Yurun\Swoole\SharedMemory\Client\Client
    {
        return $this->client;
    }

    /**
     * 获取操作对象
     */
    public function getObject(string $objectName): ?object
    {
        return $this->objects[$objectName] ?? null;
    }
}
