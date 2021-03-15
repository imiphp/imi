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
     *
     * @var string
     */
    private string $itemType = '';

    /**
     * 数组列表.
     *
     * @var array
     */
    private array $list = [];

    public function __construct(string $itemType, array $list = [])
    {
        $this->itemType = $itemType;
        foreach ($list as $item)
        {
            $this[] = $item;
        }
    }

    /**
     * @param mixed $offset
     *
     * @return bool
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
     *
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        if (!$value instanceof $this->itemType)
        {
            throw new \InvalidArgumentException('ArrayList item must be an instance of ' . $this->itemType);
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
     *
     * @return void
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

    /**
     * @return void
     */
    public function next(): void
    {
        next($this->list);
    }

    /**
     * @return void
     */
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
     *
     * @return array
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
     *
     * @return void
     */
    public function remove(...$value): void
    {
        $this->list = ArrayUtil::remove($this->list, ...$value);
    }

    /**
     * 清空.
     *
     * @return void
     */
    public function clear(): void
    {
        $this->list = [];
    }

    /**
     * 加入数组列表.
     *
     * @param mixed ...$value
     *
     * @return void
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
     *
     * @return int
     */
    public function count(): int
    {
        return \count($this->list);
    }
}
