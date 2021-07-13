<?php

declare(strict_types=1);

namespace Imi\Hprose\Client;

use Imi\Rpc\Client\IRpcClient;
use Imi\Rpc\Client\IService;

class HproseService implements IService
{
    /**
     * 客户端.
     */
    protected IRpcClient $client;

    /**
     * 服务名称.
     */
    protected string $name;

    public function __construct(IRpcClient $client, string $name)
    {
        $this->client = $client;
        $this->name = $name;
    }

    /**
     * 获取服务名称.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * 调用服务
     *
     * @param string $method 方法名
     * @param array  $args   参数
     *
     * @return mixed
     */
    public function call(string $method, array $args = [])
    {
        return $this->client->getInstance()->{$this->name}->$method(...$args);
    }

    /**
     * 魔术方法.
     *
     * @param string $name      方法名
     * @param array  $arguments 参数
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->call($name, $arguments);
    }

    /**
     * 获取客户端对象
     */
    public function getClient(): IRpcClient
    {
        return $this->client;
    }
}
