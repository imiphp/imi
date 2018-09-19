<?php
namespace Imi\Util;

use Imi\Util\Interfaces\IArrayable;

/**
 * 同时可以作为数组和对象访问的类
 */
class LazyArrayObject implements \Iterator, \ArrayAccess, IArrayable, \JsonSerializable
{
    /**
     * 数据
     * @var array
     */
    private $data;

    public function __construct($data = [])
    {
        $this->data = $data;
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function &offsetGet($offset)
    {
        if(isset($this->data[$offset]))
        {
            $value = &$this->data[$offset];
        }
        else
        {
            $value = null;
        }
        return $value;
    }

    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        if(isset($this->data[$offset]))
        {
            unset($this->data[$offset]);
        }
    }

    public function current()
    {
        return current($this->data);
    }

    public function key()
    {
        return key($this->data);
    }

    public function next()
    {
        next($this->data);
    }

    public function rewind()
    {
        reset($this->data);
    }

    public function valid()
    {
        return null !== key($this->data);
    }

    public function __set($name, $value) 
    {
        $this->data[$name] = $value;
    }

    public function &__get($name)
    {
        if(isset($this->data[$name]))
        {
            $value = &$this->data[$name];
        }
        else
        {
            $value = null;
        }
        return $value;
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function __unset($name)
    {
        if(isset($this->data[$name]))
        {
            unset($this->data[$name]);
        }
    }

    /**
     * 将当前对象作为数组返回
     * @return array
     */
    public function toArray(): array
    {
        return \iterator_to_array($this);
    }

    /**
     * json 序列化
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}