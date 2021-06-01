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
    public function __construct($options);

    /**
     * 打开
     *
     * @return bool
     */
    public function open();

    /**
     * 关闭.
     *
     * @return void
     */
    public function close();

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
     * @param string $name 服务名
     *
     * @return \Imi\Rpc\Client\IService
     */
    public function getService($name = null): IService;

    /**
     * 获取配置.
     *
     * @return array
     */
    public function getOptions();
}
