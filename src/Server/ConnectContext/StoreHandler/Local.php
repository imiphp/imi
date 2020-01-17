<?php
namespace Imi\Server\ConnectContext\StoreHandler;

use Imi\Lock\Lock;
use Imi\Bean\Annotation\Bean;
use Imi\Server\ConnectContext\StoreHandler\IHandler;

/**
 * 连接上下文存储处理器-Local
 * @Bean("ConnectContextLocal")
 */
class Local implements IHandler
{
    /**
     * 存储集合
     *
     * @var array
     */
    private $storeMap = [];

    /**
     * 锁 ID
     *
     * @var string
     */
    protected $lockId;

    /**
     * 读取数据
     *
     * @param string $key
     * @return array
     */
    public function read(string $key): array
    {
        var_dump($key);
        return $this->storeMap[$key] ?? [];
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
        $this->storeMap[$key] = $data;
    }

    /**
     * 销毁数据
     *
     * @param string $key
     * @return void
     */
    public function destroy(string $key)
    {
        if(isset($this->storeMap[$key]))
        {
            unset($this->storeMap[$key]);
        }
    }

    /**
     * 数据是否存在
     *
     * @param string $key
     * @return void
     */
    public function exists(string $key)
    {
        return isset($this->storeMap[$key]);
    }

    /**
     * 加锁
     *
     * @param callable $callable
     * @return boolean
     */
    public function lock($callable = null)
    {
        return Lock::lock($this->lockId, $callable);
    }

    /**
     * 解锁
     *
     * @return boolean
     */
    public function unlock()
    {
        return Lock::unlock($this->lockId);
    }

}
