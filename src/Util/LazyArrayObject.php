<?php

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

    /**
     * @param array $data
     */
    public function __construct($data = [])
    {
        $this->__data = $data;
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return \array_key_exists($offset, $this->__data);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
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

    /**
     * @param mixed $offset
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->__data[$offset] = $value;
    }

    /**
     * @param mixed $offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        $data = &$this->__data;
        if (\array_key_exists($offset, $data))
        {
            unset($data[$offset]);
        }
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return current($this->__data);
    }

    /**
     * @return mixed
     */
    public function key()
    {
        return key($this->__data);
    }

    /**
     * @return void
     */
    public function next()
    {
        next($this->__data);
    }

    /**
     * @return void
     */
    public function rewind()
    {
        reset($this->__data);
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return null !== key($this->__data);
    }

    /**
     * @param mixed $name
     * @param mixed $value
     *
     * @return void
     */
    public function __set($name, $value)
    {
        $this->__data[$name] = $value;
    }

    /**
     * @param mixed $name
     *
     * @return mixed
     */
    public function &__get($name)
    {
        return $this[$name];
    }

    /**
     * @param mixed $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return \array_key_exists($name, $this->__data);
    }

    /**
     * @param mixed $name
     *
     * @return bool
     */
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
