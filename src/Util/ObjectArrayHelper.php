<?php

declare(strict_types=1);

namespace Imi\Util;

use stdClass;

/**
 * 对象及数组帮助类
 * 智能识别数组和对象，支持对a.b.c这样的name属性进行操作.
 */
class ObjectArrayHelper
{
    private function __construct()
    {
    }

    /**
     * 获取值
     *
     * @param array|object $object
     * @param mixed        $default
     *
     * @return mixed
     */
    public static function get(&$object, string $name, $default = null)
    {
        $names = explode('.', $name);
        // @phpstan-ignore-next-line
        if ($names)
        {
            $result = &$object;
            foreach ($names as $nameItem)
            {
                if (\is_array($result))
                {
                    // 数组
                    if (isset($result[$nameItem]))
                    {
                        $result = &$result[$nameItem];
                    }
                    else
                    {
                        return $default;
                    }
                }
                elseif (\is_object($result))
                {
                    // 对象
                    if (isset($result->$nameItem))
                    {
                        $result = &$result->$nameItem;
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

        // @phpstan-ignore-next-line
        return $default;
    }

    /**
     * 设置值
     *
     * @param array|object $object
     * @param mixed        $value
     */
    public static function set(&$object, string $name, $value): void
    {
        $names = explode('.', $name);
        $lastName = array_pop($names);
        $data = &$object;
        foreach ($names as $nameItem)
        {
            if (\is_array($data))
            {
                $data[$nameItem] ??= [];
                $data = &$data[$nameItem];
            }
            elseif (\is_object($data))
            {
                $data->$nameItem ??= new stdClass();
                $data = &$data->$nameItem;
            }
        }
        if (\is_array($data))
        {
            $data[$lastName] = $value;
        }
        elseif (\is_object($data))
        {
            $data->$lastName = $value;
        }
    }

    /**
     * 移除值
     *
     * @param array|object $object
     */
    public static function remove(&$object, string $name): void
    {
        $names = explode('.', $name);
        $lastName = array_pop($names);
        $data = &$object;
        foreach ($names as $nameItem)
        {
            if (\is_array($data))
            {
                $data[$nameItem] ??= [];
                $data = &$data[$nameItem];
            }
            elseif (\is_object($data))
            {
                $data->$nameItem ??= new stdClass();
                $data = &$data->$nameItem;
            }
        }
        if (\is_array($data))
        {
            unset($data[$lastName]);
        }
        elseif (\is_object($data))
        {
            unset($data->$lastName);
        }
    }

    /**
     * 值是否存在.
     *
     * @param array|object $object
     */
    public static function exists($object, string $name): bool
    {
        return null !== static::get($object, $name);
    }

    /**
     * 将第二纬某字段值放入到一个数组中
     * 功能类似array_column，这个方法也支持对象
     */
    public static function column(array $array, string $columnName): array
    {
        $result = [];
        foreach ($array as $row)
        {
            if (\is_object($row))
            {
                $result[] = $row->$columnName;
            }
            else
            {
                $result[] = $row[$columnName];
            }
        }

        return $result;
    }

    /**
     * 过滤属性.
     *
     * $mode只允许取值为：allow/deny
     *
     * @param array|object $object
     */
    public static function filter(&$object, array $fields, string $mode = 'allow'): void
    {
        if ('allow' === $mode)
        {
            $unsetKeys = [];
            foreach ($object as $field => $value)
            {
                if (!\in_array($field, $fields))
                {
                    $unsetKeys[] = $field;
                }
            }
            foreach ($unsetKeys as $key)
            {
                static::remove($object, $key);
            }
        }
        elseif ('deny' === $mode)
        {
            foreach ($fields as $field)
            {
                static::remove($object, $field);
            }
        }
        else
        {
            throw new \InvalidArgumentException(sprintf('Unknow mode %s', $mode));
        }
    }
}
