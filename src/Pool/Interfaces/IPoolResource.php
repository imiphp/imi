<?php

namespace Imi\Pool\Interfaces;


/**
 * 池子资源接口
 */
interface IPoolResource
{
    /**
     * 获取对象实例
     * @return mixed
     */
    public function getInstance();

    /**
     * 打开
     *
     * @param callable $callback
     *
     * @return boolean
     */
    public function open($callback = null);

    /**
     * 关闭
     * @return void
     */
    public function close();

    /**
     * 重置资源，当资源被使用后重置一些默认的设置
     * @return void
     */
    public function reset();

    /**
     * 获取池子实例
     * @return IPool
     */
    public function getPool(): IPool;


    /**
     * 检查资源是否可用
     * @return bool
     */
    public function checkState(): bool;

    /**
     * 获取hashcode
     * @return string
     */
    public function hashCode(): string;
}