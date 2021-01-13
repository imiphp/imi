<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\ConnectContext\StoreHandler;

use Imi\Bean\Annotation\Bean;
use Imi\Lock\Lock;
use Swoole\Timer;

/**
 * 连接上下文存储处理器-Local.
 *
 * @Bean("ConnectContextLocal")
 */
class Local implements IHandler
{
    /**
     * 存储集合.
     *
     * @var array
     */
    private array $storeMap = [];

    /**
     * 锁 ID.
     *
     * @var string|null
     */
    protected ?string $lockId = null;

    /**
     * 读取数据.
     *
     * @param string $key
     *
     * @return array
     */
    public function read(string $key): array
    {
        return $this->storeMap[$key] ?? [];
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
        $this->storeMap[$key] = $data;
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
        $storeMap = &$this->storeMap;
        if (isset($storeMap[$key]))
        {
            unset($storeMap[$key]);
        }
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
        Timer::after($ttl * 1000, function () use ($key) {
            $this->destroy($key);
        });
    }

    /**
     * 数据是否存在.
     *
     * @param string $key
     *
     * @return bool
     */
    public function exists(string $key): bool
    {
        return isset($this->storeMap[$key]);
    }

    /**
     * 加锁
     *
     * @param string        $key
     * @param callable|null $callable
     *
     * @return bool
     */
    public function lock(string $key, ?callable $callable = null): bool
    {
        return Lock::getInstance($this->lockId, $key)->lock($callable);
    }

    /**
     * 解锁
     *
     * @return bool
     */
    public function unlock(): bool
    {
        return Lock::unlock($this->lockId);
    }
}
