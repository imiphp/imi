<?php

declare(strict_types=1);

namespace Imi\Rpc\Client;

interface IService
{
    /**
     * 获取服务名称.
     */
    public function getName(): ?string;

    /**
     * 调用服务
     *
     * @param string $method 方法名
     * @param array  $args   参数
     */
    public function call(string $method, array $args = []): mixed;

    /**
     * 获取客户端对象
     */
    public function getClient(): IRpcClient;
}
