<?php

declare(strict_types=1);

namespace Imi\Rpc\Client;

interface IRpcClient
{
    /**
     * 构造方法.
     *
     * @param array $options 配置
     */
    public function __construct(array $options);

    /**
     * 打开
     */
    public function open(): bool;

    /**
     * 关闭.
     */
    public function close(): void;

    /**
     * 是否已连接.
     */
    public function isConnected(): bool;

    /**
     * 获取实例对象
     *
     * @return mixed
     */
    public function getInstance();

    /**
     * 获取服务对象
     *
     * @param string|null $name 服务名
     *
     * @return \Imi\Rpc\Client\IService
     */
    public function getService(?string $name = null): IService;

    /**
     * 获取配置.
     */
    public function getOptions(): array;
}
