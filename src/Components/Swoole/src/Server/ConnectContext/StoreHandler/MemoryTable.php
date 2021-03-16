<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\ConnectContext\StoreHandler;

use Imi\Bean\Annotation\Bean;
use Imi\Lock\Lock;
use Imi\Server\ConnectContext\StoreHandler\IHandler;
use Imi\Swoole\Util\MemoryTableManager;
use Swoole\Timer;

/**
 * 连接上下文存储处理器-MemoryTable.
 *
 * @Bean("ConnectContextMemoryTable")
 */
class MemoryTable implements IHandler
{
    /**
     * 数据写入前编码回调.
     *
     * @var callable|null
     */
    protected $dataEncode = 'serialize';

    /**
     * 数据读出后处理回调.
     *
     * @var callable|null
     */
    protected $dataDecode = 'unserialize';

    /**
     * 表名.
     */
    protected string $tableName = '';

    /**
     * 锁 ID.
     */
    protected ?string $lockId = null;

    /**
     * 读取数据.
     */
    public function read(string $key): array
    {
        $result = MemoryTableManager::get($this->tableName, $key, 'data');
        if ($result)
        {
            if ($this->dataDecode)
            {
                return ($this->dataDecode)($result);
            }
            else
            {
                return $result;
            }
        }
        else
        {
            return [];
        }
    }

    /**
     * 保存数据.
     */
    public function save(string $key, array $data): void
    {
        if ($this->dataEncode)
        {
            $data = ($this->dataEncode)($data);
        }
        MemoryTableManager::set($this->tableName, $key, ['data' => $data]);
    }

    /**
     * 销毁数据.
     */
    public function destroy(string $key): void
    {
        MemoryTableManager::del($this->tableName, $key);
    }

    /**
     * 延迟销毁数据.
     */
    public function delayDestroy(string $key, int $ttl): void
    {
        Timer::after($ttl * 1000, function () use ($key) {
            $this->destroy($key);
        });
    }

    /**
     * 数据是否存在.
     */
    public function exists(string $key): bool
    {
        return MemoryTableManager::exist($this->tableName, $key);
    }

    /**
     * 加锁
     */
    public function lock(string $key, ?callable $callable = null): bool
    {
        if ($this->lockId)
        {
            return Lock::getInstance($this->lockId, $key)->lock($callable);
        }
        else
        {
            return MemoryTableManager::lock($this->tableName, $callable);
        }
    }

    /**
     * 解锁
     */
    public function unlock(): bool
    {
        if ($this->lockId)
        {
            return Lock::unlock($this->lockId);
        }
        else
        {
            return MemoryTableManager::unlock($this->tableName);
        }
    }
}
