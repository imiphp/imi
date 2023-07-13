<?php

declare(strict_types=1);

namespace Imi\Swoole\Util;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Config;
use Imi\Lock\Lock;
use Imi\Model\Annotation\Column;
use Imi\Util\MemoryTable\IMemoryTableOption;

/**
 * 跨进程共享内存表.
 */
class MemoryTableManager
{
    /**
     * \Swoole\Table 数组.
     */
    private static array $tables = [];

    /**
     * 是否初始化.
     */
    private static bool $inited = false;

    private function __construct()
    {
    }

    public static function init(): void
    {
        self::$tables = [];
        // 初始化内存表模型
        foreach (AnnotationManager::getAnnotationPoints(\Imi\Model\Annotation\MemoryTable::class, 'class') as $item)
        {
            /** @var \Imi\Model\Annotation\MemoryTable $memoryTableAnnotation */
            $memoryTableAnnotation = $item->getAnnotation();
            self::addName($memoryTableAnnotation->name, [
                'size'                  => $memoryTableAnnotation->size,
                'conflictProportion'    => $memoryTableAnnotation->conflictProportion,
                'columns'               => self::getMemoryTableColumns(AnnotationManager::getPropertiesAnnotations($item->getClass(), Column::class)),
            ]);
        }
        // 初始化配置中的内存表
        foreach (Config::getAliases() as $alias)
        {
            foreach (Config::get($alias . '.memoryTable', []) as $name => $item)
            {
                self::addName($name, $item);
            }
        }
        /** @phpstan-ignore-next-line */
        foreach (self::$tables as $name => $option)
        {
            /** @var array|string|null $option */
            if (\is_string($option))
            {
                if (!is_subclass_of($option, IMemoryTableOption::class))
                {
                    throw new \RuntimeException(sprintf('class %s must implements interface %s', $option, IMemoryTableOption::class));
                }
                $option = [
                    'class' => $option,
                ];
            }
            if (\is_array($option))
            {
                if (isset($option['class']))
                {
                    $object = new $option['class']();
                    $option = $object->getOption($option);
                }
                $table = new \Swoole\Table($option['size'] ?? 1024, $option['conflictProportion'] ?? 0.2);
                foreach ($option['columns'] as $column)
                {
                    $table->column($column['name'], $column['type'] ?? \Swoole\Table::TYPE_STRING, $column['size'] ?? 0);
                }
                if (!$table->create())
                {
                    throw new \RuntimeException('MemoryTableManager create table failed');
                }
                self::$tables[$name]['instance'] = $table;
            }
            else
            {
                throw new \RuntimeException('MemoryTable option error');
            }
        }
        self::$inited = true;
    }

    /**
     * 处理列类型和大小.
     */
    private static function parseColumnTypeAndSize(Column $column): array
    {
        $type = $column->type;
        switch ($type)
        {
            case 'string':
                $type = \Swoole\Table::TYPE_STRING;
                $size = $column->length;
                break;
            case 'int':
                $type = \Swoole\Table::TYPE_INT;
                $size = $column->length;
                if (!\in_array($size, [1, 2, 4, 8]))
                {
                    $size = 4;
                }
                break;
            case 'float':
                $type = \Swoole\Table::TYPE_FLOAT;
                $size = 8;
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Invalid swoole table field type %s', $type));
        }

        return [$type, $size];
    }

    /**
     * 获取内存表列.
     */
    private static function getMemoryTableColumns(array $columnAnnotationsSet): array
    {
        $columns = [];

        foreach ($columnAnnotationsSet as $annotations)
        {
            $columnAnnotation = $annotations[0];
            [$type, $size] = self::parseColumnTypeAndSize($columnAnnotation);
            $columns[] = [
                'name' => $columnAnnotation->name,
                'type' => $type,
                'size' => $size,
            ];
        }

        return $columns;
    }

    /**
     * 增加内存表对象名称.
     */
    public static function addName(string $name, array $option): void
    {
        if (self::$inited)
        {
            throw new \RuntimeException('AddName failed, MemoryTableManager was inited');
        }
        self::$tables[$name] = $option;
    }

    /**
     * 设置内存表对象名称.
     *
     * @param string[] $names
     */
    public static function setNames(array $names): void
    {
        if (self::$inited)
        {
            throw new \RuntimeException('AddName failed, MemoryTableManager was inited');
        }
        foreach ($names as $key => $value)
        {
            if (\is_int($key))
            {
                self::$tables[$value] = 0;
            }
            else
            {
                self::$tables[$key] = $value;
            }
        }
    }

    /**
     * 获取所有内存表对象名称.
     */
    public static function getNames(): array
    {
        return array_keys(self::$tables);
    }

    /**
     * 获取内存表类实例.
     *
     * @param string $name 表名
     */
    public static function getInstance(string $name): \Swoole\Table
    {
        if (!self::$inited)
        {
            self::init();
        }
        if (!isset(self::$tables[$name]['instance']))
        {
            throw new \RuntimeException(sprintf('GetInstance failed, %s is not found', $name));
        }

        return self::$tables[$name]['instance'];
    }

    /**
     * 设置行的数据.
     *
     * @param string $name  表名
     * @param mixed  $value
     */
    public static function set(string $name, string $key, $value): bool
    {
        return static::getInstance($name)->set($key, $value);
    }

    /**
     * 获取一行数据.
     *
     * @param string $name 表名
     *
     * @return mixed
     */
    public static function get(string $name, string $key, ?string $field = null)
    {
        if (null === $field)
        {
            // @phpstan-ignore-next-line
            return static::getInstance($name)->get($key);
        }
        else
        {
            return static::getInstance($name)->get($key, $field);
        }
    }

    /**
     * 删除行的数据.
     *
     * @param string $name 表名
     * @param string $key  $key对应的数据不存在，将返回false
     */
    public static function del(string $name, string $key): bool
    {
        return static::getInstance($name)->del($key);
    }

    /**
     * 行数据是否存在.
     *
     * @param string $name 表名
     * @param string $key  $key对应的数据不存在，将返回false
     */
    public static function exist(string $name, string $key): bool
    {
        return static::getInstance($name)->exist($key);
    }

    /**
     * 原子自增.
     *
     * @param string    $name   表名
     * @param int|float $incrby 增量，默认为1。如果列为整形，$incrby必须为int型，如果列为浮点型，$incrby必须为float类型
     *
     * @return number
     */
    public static function incr(string $name, string $key, string $column, $incrby = 1)
    {
        return static::getInstance($name)->incr($key, $column, $incrby);
    }

    /**
     * 原子自减.
     *
     * @param string    $name   表名
     * @param int|float $incrby 减量，默认为1。如果列为整形，$incrby必须为int型，如果列为浮点型，$incrby必须为float类型
     *
     * @return number
     */
    public static function decr(string $name, string $key, string $column, $incrby = 1)
    {
        return static::getInstance($name)->decr($key, $column, $incrby);
    }

    /**
     * 获取表行数
     * 失败返回false.
     *
     * @param string $name 表名
     */
    public static function count(string $name): int
    {
        return static::getInstance($name)->count();
    }

    /**
     * 加锁操作.
     *
     * @param string   $name              表名
     * @param callable $taskCallable      加锁后执行的任务，可为空；如果不为空，则执行完后自动解锁
     * @param callable $afterLockCallable 当获得锁后执行的回调，只有当 $taskCallable 不为 null 时有效。该回调返回 true 则不执行 $taskCallable
     */
    public static function lock(string $name, ?callable $taskCallable = null, ?callable $afterLockCallable = null): bool
    {
        if (!isset(self::$tables[$name]['lockId']))
        {
            throw new \RuntimeException(sprintf('MemoryTable %s has no [lockId] option', $name));
        }

        return Lock::lock(self::$tables[$name]['lockId'], $taskCallable, $afterLockCallable);
    }

    /**
     * 解锁
     *
     * @param string $name 表名
     */
    public static function unlock(string $name): bool
    {
        if (!isset(self::$tables[$name]['lockId']))
        {
            throw new \RuntimeException(sprintf('MemoryTable %s has no [lockId] option', $name));
        }

        return Lock::unlock(self::$tables[$name]['lockId']);
    }

    /**
     * 是否已初始化过.
     */
    public static function isInited(): bool
    {
        return self::$inited;
    }
}
