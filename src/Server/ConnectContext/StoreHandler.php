<?php

namespace Imi\Server\ConnectContext;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\ConnectContext\StoreHandler\IHandler;

/**
 * 连接上下文存储处理器-总.
 *
 * @Bean("ConnectContextStore")
 */
class StoreHandler implements IHandler
{
    /**
     * 处理器类.
     *
     * @var string
     */
    protected $handlerClass = \Imi\Server\ConnectContext\StoreHandler\Redis::class;

    /**
     * 数据有效期，单位：秒
     * 连接断开后，供断线重连的，数据保留时间
     * 设为 0 则连接断开立即销毁数据.
     *
     * @var int
     */
    protected $ttl = 0;

    /**
     * 读取数据.
     *
     * @param string $key
     *
     * @return array
     */
    public function read(string $key): array
    {
        return $this->getHandler()->read($key);
    }

    /**
     * 保存数据.
     *
     * @param string $key
     * @param array  $data
     *
     * @return void
     */
    public function save(string $key, array $data)
    {
        $this->getHandler()->save($key, $data);
    }

    /**
     * 销毁数据.
     *
     * @param string $key
     *
     * @return void
     */
    public function destroy(string $key)
    {
        $this->getHandler()->destroy($key);
    }

    /**
     * 延迟销毁数据.
     *
     * @param string $key
     * @param int    $ttl
     *
     * @return void
     */
    public function delayDestroy(string $key, int $ttl)
    {
        $this->getHandler()->delayDestroy($key, $ttl);
    }

    /**
     * 数据是否存在.
     *
     * @param string $key
     *
     * @return void
     */
    public function exists(string $key)
    {
        return $this->getHandler()->exists($key);
    }

    /**
     * 加锁
     *
     * @param string   $key
     * @param callable $callable
     *
     * @return bool
     */
    public function lock(string $key, $callable = null)
    {
        return $this->getHandler()->lock($key, $callable);
    }

    /**
     * 解锁
     *
     * @return bool
     */
    public function unlock()
    {
        return $this->getHandler()->unlock();
    }

    /**
     * 获取处理器.
     *
     * @return \Imi\Server\ConnectContext\StoreHandler\IHandler
     */
    public function getHandler(): IHandler
    {
        return RequestContext::getServerBean($this->handlerClass);
    }

    /**
     * Get 设为 0 则连接断开立即销毁数据.
     *
     * @return int
     */
    public function getTtl()
    {
        return $this->ttl;
    }
}
