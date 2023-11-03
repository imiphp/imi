<?php

declare(strict_types=1);

namespace Imi\Util;

use Imi\Util\Interfaces\IArrayable;

/**
 * 过滤字段的列表，每一个成员应该是数组或对象
 */
class FilterableList implements \Iterator, \ArrayAccess, IArrayable, \JsonSerializable, \Countable
{
    /**
     * 数组列表.
     */
    private array $list = [];

    public function __construct(array $list = [],
        /**
         * 字段名数组.
         *
         * 为null则不过滤
         *
         * @var string[]|null
         */
        private readonly ?array $fields = null,
        /**
         * 模式
         * allow-白名单
         * deny-黑名单.
         */
        private readonly string $mode = 'allow')
    {
        $this->list = $this->parseList($list);
    }

    public function offsetExists(mixed $key): bool
    {
        return isset($this->list[$key]);
    }

    public function &offsetGet(mixed $key): mixed
    {
        $list = &$this->list;
        if (isset($list[$key]))
        {
            $value = &$list[$key];
        }
        else
        {
            $value = null;
        }

        return $value;
    }

    public function offsetSet(mixed $key, mixed $value): void
    {
        $values = $this->parseList([$value]);
        if (null === $key)
        {
            $this->list[] = $values[0];
        }
        else
        {
            $this->list[$key] = $values[0];
        }
    }

    public function offsetUnset(mixed $key): void
    {
        unset($this->list[$key]);
    }

    public function current(): mixed
    {
        return current($this->list);
    }

    public function key(): int|string|null
    {
        return key($this->list);
    }

    public function next(): void
    {
        next($this->list);
    }

    public function rewind(): void
    {
        reset($this->list);
    }

    public function valid(): bool
    {
        return null !== key($this->list);
    }

    /**
     * 将当前对象作为数组返回.
     */
    public function toArray(): array
    {
        return $this->list;
    }

    /**
     * json 序列化.
     *
     * @return array
     */
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    /**
     * 从数组列表中移除.
     */
    public function remove(mixed ...$value): void
    {
        $this->list = ArrayUtil::removeKeepKey($this->list, ...$value);
    }

    /**
     * 清空.
     */
    public function clear(): void
    {
        $this->list = [];
    }

    /**
     * 加入数组列表.
     */
    public function append(mixed ...$value): void
    {
        $value = $this->parseList($value);
        foreach ($value as $row)
        {
            $this[] = $row;
        }
    }

    /**
     * 数组列表长度.
     */
    public function count(): int
    {
        return \count($this->list);
    }

    /**
     * 处理列表.
     */
    private function parseList(array $list): array
    {
        if (null === $this->fields)
        {
            return $list;
        }
        $result = [];
        foreach ($list as $item)
        {
            if (\is_object($item))
            {
                $item = clone $item;
            }
            ObjectArrayHelper::filter($item, $this->fields, $this->mode);
            $result[] = $item;
        }

        return $result;
    }
}
