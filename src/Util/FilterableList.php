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
     * 模式
     * allow-白名单
     * deny-黑名单.
     */
    private string $mode = '';

    /**
     * 字段名数组.
     *
     * 为null则不过滤
     *
     * @var string[]|null
     */
    private ?array $fields = null;

    /**
     * 数组列表.
     */
    private array $list = [];

    public function __construct(array $list = [], ?array $fields = null, string $mode = 'allow')
    {
        $this->mode = $mode;
        $this->fields = $fields;
        $this->list = $this->parseList($list);
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
    #[\ReturnTypeWillChange]
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
        $list = $this->list;
        if (isset($list[$offset]))
        {
            unset($list[$offset]);
        }
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

    /**
     * @return bool
     */
    #[\ReturnTypeWillChange]
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
    #[\ReturnTypeWillChange]
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
