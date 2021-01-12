<?php

declare(strict_types=1);

namespace Imi\Util;

/**
 * 数组帮助类.
 */
class ArrayUtil
{
    private function __construct()
    {
    }

    /**
     * 从数组中移除一个或多个元素，重新组织为连续的键.
     *
     * @param array $array
     * @param mixed $value
     *
     * @return array
     */
    public static function remove(array $array, ...$value): array
    {
        foreach ($value as $item)
        {
            while (false !== ($index = array_search($item, $array)))
            {
                unset($array[$index]);
            }
        }

        return array_values($array);
    }

    /**
     * 从数组中移除一个或多个元素，保持原有键.
     *
     * @param array $array
     * @param mixed $value
     *
     * @return array
     */
    public static function removeKeepKey(array $array, ...$value): array
    {
        foreach ($value as $item)
        {
            while (false !== ($index = array_search($item, $array)))
            {
                unset($array[$index]);
            }
        }

        return $array;
    }

    /**
     * 多维数组递归合并.
     *
     * @param array ...$arrays
     *
     * @return array
     */
    public static function recursiveMerge(array ...$arrays): array
    {
        $merged = [];
        foreach ($arrays as $array)
        {
            if (!\is_array($array))
            {
                continue;
            }
            $isAssoc = self::isAssoc($array);
            foreach ($array as $key => $value)
            {
                if ($isAssoc)
                {
                    if (\is_array($value) && isset($merged[$key]) && \is_array($merged[$key]))
                    {
                        $merged[$key] = static::recursiveMerge($merged[$key], $value);
                    }
                    else
                    {
                        $merged[$key] = $value;
                    }
                }
                else
                {
                    $merged[] = $value;
                }
            }
        }

        return $merged;
    }

    /**
     * 将二维数组第二纬某key变为一维的key.
     *
     * @param array      $array   原数组
     * @param string|int $column  列名
     * @param bool       $keepOld 是否保留列名，默认保留
     *
     * @return array
     */
    public static function columnToKey(array $array, $column, bool $keepOld = true): array
    {
        $newArray = [];
        foreach ($array as $row)
        {
            $key = $row[$column];
            if (!$keepOld)
            {
                unset($row[$column]);
            }
            $newArray[$key] = $row;
        }

        return $newArray;
    }

    /**
     * 判断数组是否为关联数组.
     *
     * @param array $array
     *
     * @return bool
     */
    public static function isAssoc(array $array): bool
    {
        return array_keys($array) !== range(0, \count($array) - 1);
    }

    /**
     * 随机获得数组中的值，返回一个保持键值对应的数组.
     *
     * @param array $array
     * @param int   $number
     *
     * @return array
     */
    public static function random(array $array, int $number = 1): array
    {
        $result = [];
        $keys = array_rand($array, $number);
        foreach ($keys as $key)
        {
            if (!isset($array[$key]))
            {
                break;
            }
            $result[$key] = $array[$key];
        }

        return $result;
    }

    /**
     * 列表转树形关联结构.
     *
     * @param array  $list
     * @param string $idField
     * @param string $parentField
     * @param string $childrenField
     *
     * @return array
     */
    public static function toTreeAssoc(array $list, string $idField = 'id', string $parentField = 'parent_id', string $childrenField = 'children'): array
    {
        // 查出所有记录
        $result = $tmpArr = [];
        // 处理成ID为键名的数组
        foreach ($list as $item)
        {
            $item[$childrenField] = [];
            $tmpArr[$item[$idField]] = $item;
        }
        foreach ($tmpArr as $item)
        {
            if (isset($tmpArr[$item[$parentField]]))
            {
                $tmpArr[$item[$parentField]][$childrenField][] = &$tmpArr[$item[$idField]];
            }
            else
            {
                $result[] = &$tmpArr[$item[$idField]];
            }
        }

        return $result;
    }
}
