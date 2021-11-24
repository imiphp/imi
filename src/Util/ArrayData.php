<?php

declare(strict_types=1);

namespace Imi\Util;

/**
 * 数组数据基类.
 */
class ArrayData implements \ArrayAccess, \Countable
{
    /**
     * 数据.
     */
    protected array $__data = [];

    public function __construct(array $data)
    {
        $this->__data = $data;
    }

    /**
     * 设置数据.
     *
     * @param string|array $name
     * @param mixed        $value
     */
    public function set($name, $value = null, bool $merge = true): bool
    {
        if (\is_array($name))
        {
            if ($merge)
            {
                // 如果传入数组就合并当前数据
                $this->__data = ArrayUtil::recursiveMerge($this->__data, $name);
            }
            else
            {
                $this->__data = $name;
            }
        }
        else
        {
            // 设置数据
            $this->__data[$name] = $value;
        }

        return true;
    }

    /**
     * 设置数据.
     *
     * @param string|array $name
     * @param mixed        $value
     */
    public function setVal($name, $value = null): bool
    {
        if (\is_string($name))
        {
            $name = explode('.', $name);
        }
        elseif (!\is_array($name))
        {
            return false;
        }
        $last = array_pop($name);
        $data = &$this->__data;
        if ($name)
        {
            foreach ($name as $val)
            {
                $data[$val] ??= [];
                $data = &$data[$val];
            }
        }
        $data[$last] = $value;

        return true;
    }

    /**
     * 获取数据.
     *
     * @param string|array|null $name
     * @param mixed             $default
     *
     * @return mixed
     */
    public function &get($name = null, $default = false)
    {
        if (null === $name)
        {
            return $this->__data;
        }
        if (\is_string($name))
        {
            $name = explode('.', $name);
        }
        elseif (!\is_array($name))
        {
            return $default;
        }
        $result = &$this->__data;
        if ($name)
        {
            foreach ($name as $value)
            {
                if (\is_array($result))
                {
                    // 数组
                    if (isset($result[$value]))
                    {
                        $result = &$result[$value];
                    }
                    else
                    {
                        return $default;
                    }
                }
                elseif (\is_object($result))
                {
                    // 对象
                    if (property_exists($result, $value))
                    {
                        $result = &$result->$value;
                    }
                    else
                    {
                        return $default;
                    }
                }
                else
                {
                    return $default;
                }
            }
        }
        if (isset($value))
        {
            return $result;
        }
        else
        {
            return $default;
        }
    }

    /**
     * 删除数据.
     *
     * @param string|array $name
     */
    public function remove($name): bool
    {
        if (!\is_array($name))
        {
            $name = \func_get_args();
        }
        if ($name)
        {
            foreach ($name as $val)
            {
                if (\is_string($val))
                {
                    $val = explode('.', $val);
                }
                elseif (!\is_array($val))
                {
                    return false;
                }
                $last = array_pop($val);
                $result = &$this->__data;
                if ($val)
                {
                    foreach ($val as $value)
                    {
                        if (isset($result[$value]))
                        {
                            $result = &$result[$value];
                        }
                    }
                }
                unset($result[$last]);
            }
        }

        return true;
    }

    /**
     * 清空数据.
     */
    public function clear(): void
    {
        $this->__data = [];
    }

    /**
     * 获取数据的数量.
     */
    public function length(): int
    {
        return \count($this->__data);
    }

    /**
     * 获取数据的数量.
     */
    public function count(): int
    {
        return \count($this->__data);
    }

    /**
     * 键名对应的值是否存在.
     */
    public function exists(string $name): bool
    {
        return isset($this->__data[$name]);
    }

    /**
     * @param mixed $key
     *
     * @return mixed
     */
    public function &__get($key)
    {
        return $this->get($key);
    }

    /**
     * @param mixed $key
     * @param mixed $value
     */
    public function __set($key, $value): void
    {
        $this->set($key, $value);
    }

    /**
     * @param mixed $key
     */
    public function __isset($key): bool
    {
        return null !== $this->get($key, null);
    }

    /**
     * @param mixed $key
     */
    public function __unset($key)
    {
        $this->remove($key);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        if (null === $offset)
        {
            $this->__data[] = $value;
        }
        else
        {
            $this->setVal($offset, $value);
        }
    }

    /**
     * @param mixed $offset
     */
    public function offsetExists($offset): bool
    {
        return null !== $this->get($offset, null);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        $this->remove($offset);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function &offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function &getRawData(): array
    {
        return $this->__data;
    }
}
