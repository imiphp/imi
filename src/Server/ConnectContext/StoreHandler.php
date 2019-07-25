<?php
namespace Imi\Server\ConnectContext;

use Imi\RequestContext;
use Imi\Bean\Annotation\Bean;
use Imi\Server\ConnectContext\StoreHandler\IHandler;

/**
 * 连接上下文存储处理器-总
 * @Bean("ConnectContextStore")
 */
class StoreHandler implements IHandler
{
    /**
     * 处理器类
     *
     * @var string
     */
    protected $handlerClass = \Imi\Server\ConnectContext\StoreHandler\Redis::class;

    /**
     * 读取数据
     *
     * @param string $key
     * @return array
     */
    public function read(string $key): array
    {
        return $this->getHandler()->read($key);
    }

    /**
     * 保存数据
     *
     * @param string $key
     * @param array $data
     * @return void
     */
    public function save(string $key, array $data)
    {
        $this->getHandler()->save($key, $data);
    }

    /**
     * 销毁数据
     *
     * @param string $key
     * @return void
     */
    public function destroy(string $key)
    {
        $this->getHandler()->destroy($key);
    }

    /**
     * 数据是否存在
     *
     * @param string $key
     * @return void
     */
    public function exists(string $key)
    {
        return $this->getHandler()->exists($key);
    }

    /**
     * 加锁
     *
     * @param callable $callable
     * @return boolean
     */
    public function lock($callable = null)
    {
        return $this->getHandler()->lock($callable);
    }

    /**
     * 解锁
     *
     * @return boolean
     */
    public function unlock()
    {
        return $this->getHandler()->unlock();
    }

    /**
     * 获取处理器
     *
     * @return \Imi\Server\ConnectContext\StoreHandler\IHandler
     */
    public function getHandler(): IHandler
    {
        return RequestContext::getServerBean($this->handlerClass);
    }
}