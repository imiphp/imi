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
     */
    protected string $__key = '';

    /**
     * 查找一条记录.
     *
     * @return static|null
     */
    public static function find(string $key): ?self
    {
        /** @var MemoryTable|null $memoryTableAnnotation */
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
        $object = static::createFromRecord($data);
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
        /** @var MemoryTable|null $memoryTableAnnotation */
        $memoryTableAnnotation = ModelManager::getAnnotation(static::class, MemoryTable::class);
        if (null === $memoryTableAnnotation)
        {
            return [];
        }
        $instance = MemoryTableManager::getInstance($memoryTableAnnotation->name);

        return iterator_to_array($instance);
    }

    /**
     * 保存记录.
     */
    public function save(): void
    {
        /** @var MemoryTable|null $memoryTableAnnotation */
        $memoryTableAnnotation = ModelManager::getAnnotation($this, MemoryTable::class);
        if (null === $memoryTableAnnotation)
        {
            return;
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
     */
    public function delete(): void
    {
        /** @var MemoryTable|null $memoryTableAnnotation */
        $memoryTableAnnotation = ModelManager::getAnnotation($this, MemoryTable::class);
        if (null === $memoryTableAnnotation)
        {
            return;
        }
        MemoryTableManager::del($memoryTableAnnotation->name, $this->__key);
    }

    /**
     * 批量删除.
     */
    public static function deleteBatch(string ...$keys): void
    {
        /** @var MemoryTable|null $memoryTableAnnotation */
        $memoryTableAnnotation = ModelManager::getAnnotation(static::class, MemoryTable::class);
        if (null === $memoryTableAnnotation)
        {
            return;
        }
        // @phpstan-ignore-next-line
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
     */
    public static function count(): int
    {
        /** @var MemoryTable|null $memoryTableAnnotation */
        $memoryTableAnnotation = ModelManager::getAnnotation(static::class, MemoryTable::class);
        if (null === $memoryTableAnnotation)
        {
            return 0;
        }

        return MemoryTableManager::count($memoryTableAnnotation->name);
    }

    /**
     * 获取键.
     */
    public function __getKey(): string
    {
        return $this->__key;
    }

    /**
     * 设置键.
     *
     * @return static
     */
    public function __setKey(string $key): self
    {
        $this->__key = $key;

        return $this;
    }

    public function __serialize(): array
    {
        $result = parent::__serialize();
        $result['key'] = $this->__key;

        return $result;
    }

    public function __unserialize(array $data): void
    {
        parent::__unserialize($data);
        ['key' => $this->__key] = $data;
    }
}
