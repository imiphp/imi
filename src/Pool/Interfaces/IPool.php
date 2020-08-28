<?php
namespace Imi\Pool\Interfaces;

/**
 * 池子接口
 */
interface IPool
{
    /**
     * 获取池子名称
     *
     * @return string
     */
    public function getName(): string;

    /**
     * 获取池子配置
     * @return IPoolConfig
     */
    public function getConfig(): IPoolConfig;
    
    /**
     * 打开池子
     * @return void
     */
    public function open();

    /**
     * 关闭池子，释放所有资源
     * @return void
     */
    public function close();

    /**
     * 获取资源
     * @return IPoolResource
     */
    public function getResource();

    /**
     * 尝试获取资源，获取到则返回资源，没有获取到返回false
     * @return IPoolResource|boolean
     */
    public function tryGetResource();

    /**
     * 释放资源占用
     * @param IPoolResource $resource
     * @return void
     */
    public function release(IPoolResource $resource);

    /**
     * 资源回收
     * @return void
     */
    public function gc();

    /**
     * 填充最少资源数量
     * @return void
     */
    public function fillMinResources();

    /**
     * 获取当前池子中资源总数
     * @return int
     */
    public function getCount();

    /**
     * 获取当前池子中空闲资源总数
     * @return int
     */
    public function getFree();

    /**
     * 获取当前池子中正在使用的资源总数
     * @return int
     */
    public function getUsed();

    /**
     * 获得资源配置
     * @return mixed
     */
    public function getResourceConfig();

}