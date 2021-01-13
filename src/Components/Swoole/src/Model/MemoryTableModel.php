<?php

declare(strict_types=1);

namespace Imi\Swoole\Model;

use Imi\Model\Annotation\MemoryTable;
use Imi\Model\BaseModel;
use Imi\Model\ModelManager;
use Imi\Swoole\Util\MemoryTableManager;

/**
 * Swoole Table 模型.
 */
abstract class MemoryTableModel extends BaseModel
{
    /**
     * 记录的key值
     *
     * @var string
     */
    protected string $__key;

    /**
     * 查找一条记录.
     *
     * @param string $key
     *
     * @return static
     */
    public static function find(string $key): self
    {
        $memoryTableAnnotation = ModelManager::getAnnotation(static::class, MemoryTable::class);
        if (null === $memoryTableAnnotation)
        {
            return null;
        }
        $data = MemoryTableManager::get($memoryTableAnnotation->name, $key);
        if (false === $data)
        {
            return null;
        }
        $object = static::newInstance($data);
        $object->__setKey($key);

        return $object;
    }

    /**
     * 查询多条记录.
     *
     * @return static[]
     */
    public static function select(): array
    {
        $memoryTableAnnotation = ModelManager::getAnnotation(static::class, MemoryTable::class);
        if (null === $memoryTableAnnotation)
        {
            return null;
        }
        $instance = MemoryTableManager::getInstance($memoryTableAnnotation->name);

        return iterator_to_array($instance);
    }

    /**
     * 保存记录.
     *
     * @return void
     */
    public function save()
    {
        $memoryTableAnnotation = ModelManager::getAnnotation($this, MemoryTable::class);
        if (null === $memoryTableAnnotation)
        {
            return null;
        }
        $data = [];
        foreach ($this->__fieldNames as $fieldName)
        {
            $data[$fieldName] = $this[$fieldName];
        }
        MemoryTableManager::set($memoryTableAnnotation->name, $this->__key, $data);
    }

    /**
     * 删除记录.
     *
     * @return void
     */
    public function delete()
    {
        $memoryTableAnnotation = ModelManager::getAnnotation($this, MemoryTable::class);
        if (null === $memoryTableAnnotation)
        {
            return null;
        }
        MemoryTableManager::del($memoryTableAnnotation->name, $this->__key);
    }

    /**
     * 批量删除.
     *
     * @param string ...$keys
     *
     * @return void
     */
    public static function deleteBatch(string ...$keys)
    {
        $memoryTableAnnotation = ModelManager::getAnnotation(static::class, MemoryTable::class);
        if (null === $memoryTableAnnotation)
        {
            return null;
        }
        if (isset($keys[0]) && \is_array($keys[0]))
        {
            $keys = $keys[0];
        }
        foreach ($keys as $key)
        {
            MemoryTableManager::del($memoryTableAnnotation->name, $key);
        }
    }

    /**
     * 统计数量.
     *
     * @return int
     */
    public static function count(): int
    {
        $memoryTableAnnotation = ModelManager::getAnnotation(static::class, MemoryTable::class);
        if (null === $memoryTableAnnotation)
        {
            return null;
        }

        return MemoryTableManager::count($memoryTableAnnotation->name);
    }

    /**
     * 获取键.
     *
     * @return string
     */
    public function __getKey(): string
    {
        return $this->__key;
    }

    /**
     * 设置键.
     *
     * @param string $key
     *
     * @return static
     */
    public function __setKey(string $key): self
    {
        $this->__key = $key;

        return $this;
    }
}
