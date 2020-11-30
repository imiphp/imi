<?php

declare(strict_types=1);

namespace Imi\Util;

use Imi\Util\Interfaces\IArrayable;

/**
 * 同时可以作为数组和对象访问的类.
 */
class LazyArrayObject implements \Iterator, \ArrayAccess, IArrayable, \JsonSerializable
{
    /**
     * 数据.
     *
     * @var array
     */
    private $__data;

    public function __construct($data = [])
    {
        $this->__data = $data;
    }

    public function offsetExists($offset)
    {
        return \array_key_exists($offset, $this->__data);
    }

    public function &offsetGet($offset)
    {
        $data = &$this->__data;
        if (\array_key_exists($offset, $data))
        {
            $value = &$data[$offset];
        }
        else
        {
            $value = null;
        }

        return $value;
    }

    public function offsetSet($offset, $value)
    {
        $this->__data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        $data = &$this->__data;
        if (\array_key_exists($offset, $data))
        {
            unset($data[$offset]);
        }
    }

    public function current()
    {
        return current($this->__data);
    }

    public function key()
    {
        return key($this->__data);
    }

    public function next()
    {
        next($this->__data);
    }

    public function rewind()
    {
        reset($this->__data);
    }

    public function valid()
    {
        return null !== key($this->__data);
    }

    public function __set($name, $value)
    {
        $this->__data[$name] = $value;
    }

    public function &__get($name)
    {
        return $this[$name];
    }

    public function __isset($name)
    {
        return \array_key_exists($name, $this->__data);
    }

    public function __unset($name)
    {
        $data = &$this->__data;
        if (\array_key_exists($name, $data))
        {
            unset($data[$name]);
        }
    }

    /**
     * 将当前对象作为数组返回.
     *
     * @return array
     */
    public function toArray(): array
    {
        return iterator_to_array($this);
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
}
