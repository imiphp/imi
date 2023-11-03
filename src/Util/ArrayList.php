<?php

declare(strict_types=1);

namespace Imi\Util;

use Imi\Util\Interfaces\IArrayable;

/**
 * 限定成员类型的数组列表.
 */
class ArrayList implements \Iterator, \ArrayAccess, IArrayable, \JsonSerializable, \Countable
{
    /**
     * 数组列表.
     */
    private array $list = [];

    public function __construct(
        /**
         * 限定的数组列表成员类型.
         */
        private readonly string $itemType, array $list = [])
    {
        if ($list)
        {
            foreach ($list as $item)
            {
                $this[] = $item;
            }
        }
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->list[$offset]);
    }

    /**
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function &offsetGet(mixed $offset)
    {
        $list = &$this->list;
        if (isset($list[$offset]))
        {
            $value = &$list[$offset];
        }
        else
        {
            $value = null;
        }

        return $value;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!$value instanceof $this->itemType)
        {
            $type = \gettype($value);
            throw new \InvalidArgumentException('ArrayList item must be an instance of ' . $this->itemType . ', ' . ('object' === $type ? $value::class : $type) . ' given');
        }
        if (null === $offset)
        {
            $this->list[] = $value;
        }
        else
        {
            $this->list[$offset] = $value;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->list[$offset]);
    }

    /**
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return current($this->list);
    }

    /**
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function key()
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
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * 从数组列表中移除.
     */
    public function remove(mixed ...$value): void
    {
        $this->list = ArrayUtil::remove($this->list, ...$value);
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
}
