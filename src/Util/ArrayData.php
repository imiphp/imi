<?php

declare(strict_types=1);

namespace Imi\Util;

/**
 * 数组数据基类.
 */
class ArrayData implements \ArrayAccess, \Countable
{
    public function __construct(
        /**
         * 数据.
         */
        protected array $__data
    ) {
    }

    /**
     * 设置数据.
     */
    public function set(string|int|array $name, mixed $value = null, bool $merge = true): bool
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
     */
    public function setVal(string|int|array $name, mixed $value = null): bool
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
     */
    public function &get(string|int|array|null $name = null, mixed $default = false): mixed
    {
        if (null === $name)
        {
            return $this->__data;
        }
        if (\is_string($name))
        {
            $name = explode('.', $name);
            // TODO: 3.0 去除判断
            // @phpstan-ignore-next-line
            if (false === $name)
            {
                // @codeCoverageIgnoreStart
                return $default;
                // @codeCoverageIgnoreEnd
            }
        }
        elseif (!\is_array($name))
        {
            return $default;
        }
        $result = &$this->__data;
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
                    $result = &$result->{$value};
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

        return $result;
    }

    /**
     * 删除数据.
     */
    public function remove(string|int|array $name): bool
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
                if (!\is_array($val))
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
    public function exists(string|int $name): bool
    {
        return isset($this->__data[$name]);
    }

    public function &__get(mixed $name): mixed
    {
        return $this->get($name);
    }

    public function __set(mixed $name, mixed $value): void
    {
        $this->set($name, $value);
    }

    public function __isset(mixed $name): bool
    {
        return null !== $this->get($name, null);
    }

    public function __unset(mixed $name): void
    {
        $this->remove($name);
    }

    public function offsetSet(mixed $key, mixed $value): void
    {
        if (null === $key)
        {
            $this->__data[] = $value;
        }
        else
        {
            $this->setVal($key, $value);
        }
    }

    public function offsetExists(mixed $key): bool
    {
        return null !== $this->get($key, null);
    }

    public function offsetUnset(mixed $key): void
    {
        $this->remove($key);
    }

    public function &offsetGet(mixed $key): mixed
    {
        return $this->get($key);
    }

    public function &getRawData(): array
    {
        return $this->__data;
    }
}
