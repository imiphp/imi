<?php

declare(strict_types=1);

namespace Imi\Pool\Interfaces;

/**
 * 池子接口.
 */
interface IPool
{
    /**
     * 获取池子名称.
     */
    public function getName(): string;

    /**
     * 获取池子配置.
     */
    public function getConfig(): IPoolConfig;

    /**
     * 打开池子.
     */
    public function open(): void;

    /**
     * 关闭池子，释放所有资源.
     */
    public function close(): void;

    /**
     * 获取资源.
     */
    public function getResource(): IPoolResource;

    /**
     * 尝试获取资源，获取到则返回资源，没有获取到返回false.
     *
     * @return IPoolResource|bool
     */
    public function tryGetResource();

    /**
     * 创建一个不受连接池管理的资源.
     */
    public function createNewResource(): IPoolResource;

    /**
     * 释放资源占用.
     */
    public function release(IPoolResource $resource): void;

    /**
     * 从连接池移除资源.
     */
    public function removeResource(IPoolResource $resource, bool $buildQueue = false): void;

    /**
     * 资源回收.
     */
    public function gc(): void;

    /**
     * 填充最少资源数量.
     */
    public function fillMinResources(): void;

    /**
     * 获取当前池子中资源总数.
     */
    public function getCount(): int;

    /**
     * 获取当前池子中空闲资源总数.
     */
    public function getFree(): int;

    /**
     * 获取当前池子中正在使用的资源总数.
     */
    public function getUsed(): int;

    /**
     * 获得资源配置.
     *
     * @return mixed
     */
    public function getResourceConfig();
}
