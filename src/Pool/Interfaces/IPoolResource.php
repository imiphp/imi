<?php

declare(strict_types=1);

namespace Imi\Pool\Interfaces;

use Imi\Util\Interfaces\IHashCode;

/**
 * 池子资源接口.
 */
interface IPoolResource extends IHashCode
{
    /**
     * 打开
     */
    public function open(): bool;

    /**
     * 关闭.
     */
    public function close(): void;

    /**
     * 获取对象实例.
     *
     * @return mixed
     */
    public function getInstance();

    /**
     * 获取池子实例.
     */
    public function getPool(): IPool;

    /**
     * 重置资源，当资源被使用后重置一些默认的设置.
     */
    public function reset(): void;

    /**
     * 检查资源是否可用.
     */
    public function checkState(): bool;
}
