<?php
namespace Imi\Server\ConnectContext\StoreHandler;

use Imi\Bean\Annotation\Bean;
use Imi\Util\MemoryTableManager;
use Imi\Lock\Lock;

/**
 * 连接上下文存储处理器-MemoryTable
 * @Bean("ConnectContextMemoryTable")
 */
class MemoryTable implements IHandler
{
    /**
     * 数据写入前编码回调
     *
     * @var callable
     */
    protected $dataEncode = 'serialize';

    /**
     * 数据读出后处理回调
     *
     * @var callable
     */
    protected $dataDecode = 'unserialize';

    /**
     * 表名
     *
     * @var string
     */
    protected $tableName;

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
        $result = MemoryTableManager::get($this->tableName, $key, 'data');
        if($result)
        {
            if($this->dataDecode)
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
     * 保存数据
     *
     * @param string $key
     * @param array $data
     * @return void
     */
    public function save(string $key, array $data)
    {
        if($this->dataEncode)
        {
            $data = ($this->dataEncode)($data);
        }
        MemoryTableManager::set($this->tableName, $key, ['data' => $data]);
    }

    /**
     * 销毁数据
     *
     * @param string $key
     * @return void
     */
    public function destroy(string $key)
    {
        MemoryTableManager::del($this->tableName, $key);
    }

    /**
     * 数据是否存在
     *
     * @param string $key
     * @return void
     */
    public function exists(string $key)
    {
        return MemoryTableManager::exist($this->tableName, $key);
    }
    
    /**
     * 加锁
     * $callable 不为 null 时，应在执行完后自动解锁
     *
     * @param callable $callable
     * @return boolean
     */
    public function lock($callable = null)
    {
        if($this->lockId)
        {
            return Lock::lock($this->lockId, $callable);
        }
        else
        {
            return MemoryTableManager::lock($this->tableName, $callable);
        }
    }

    /**
     * 解锁
     *
     * @return boolean
     */
    public function unlock()
    {
        if($this->lockId)
        {
            return Lock::unlock($this->lockId);
        }
        else
        {
            return MemoryTableManager::unlock($this->tableName);
        }
    }

}