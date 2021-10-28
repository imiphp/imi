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
     * 限定的数组列表成员类型.
     */
    private string $itemType = '';

    /**
     * 数组列表.
     */
    private array $list = [];

    public function __construct(string $itemType, array $list = [])
    {
        $this->itemType = $itemType;
        if ($list)
        {
            foreach ($list as $item)
            {
                $this[] = $item;
            }
        }
    }

    /**
     * @param mixed $offset
     */
    public function offsetExists($offset): bool
    {
        return isset($this->list[$offset]);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function &offsetGet($offset)
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

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        if (!$value instanceof $this->itemType)
        {
            $type = \gettype($value);
            throw new \InvalidArgumentException('ArrayList item must be an instance of ' . $this->itemType . ', ' . ('object' === $type ? \get_class($value) : $type) . ' given');
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

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        $list = &$this->list;
        if (isset($list[$offset]))
        {
            unset($list[$offset]);
        }
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return current($this->list);
    }

    /**
     * @return mixed
     */
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

    /**
     * @return bool
     */
    public function valid()
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
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * 从数组列表中移除.
     *
     * @param mixed ...$value
     */
    public function remove(...$value): void
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
     *
     * @param mixed ...$value
     */
    public function append(...$value): void
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
