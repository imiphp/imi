<?php

declare(strict_types=1);

namespace Imi\SharedMemory;

class Client
{
    /**
     * 客户端对象
     *
     * @var \Yurun\Swoole\SharedMemory\Client\Client
     */
    private $client;

    /**
     * 存储器数组.
     *
     * @var array
     */
    private $objects = [];

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
     *
     * @return \Yurun\Swoole\SharedMemory\Client\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * 获取操作对象
     *
     * @param string $objectName
     *
     * @return object
     */
    public function getObject($objectName)
    {
        return $this->objects[$objectName] ?? null;
    }
}
