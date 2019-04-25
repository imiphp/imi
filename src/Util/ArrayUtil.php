<?php
namespace Imi\Util;

/**
 * 数组帮助类
 */
abstract class ArrayUtil
{
    /**
     * 从数组中移除一个或多个元素
     * @param array $array
     * @param mixed $value
     * @return array
     */
    public static function remove($array, ...$value)
    {
        return array_diff($array, $value);
    }

    /**
     * 多维数组递归合并
     * @param array ...$arrays
     * @return array
     */
    public static function recursiveMerge(...$arrays)
    {
        $merged = array ();
        foreach($arrays as $array)
        {
            if (!is_array($array))
            {
                continue;
            }
            foreach ( $array as $key => $value )
            {
                if (is_string ( $key ))
                {
                    if (is_array ( $value ) && isset($merged[$key]) && is_array ( $merged [$key] ))
                    {
                        $merged [$key] = static::recursiveMerge ( $merged [$key], $value );
                    }
                    else
                    {
                        $merged [$key] = $value;
                    }
                }
                else
                {
                    $merged [] = $value;
                }
            }
        }
        return $merged;
    }

    /**
     * 将二维数组第二纬某key变为一维的key
     * @param array $array 原数组
     * @param string $column 列名
     * @param boolean $keepOld 是否保留列名，默认保留
     * @return array
     */
    public static function columnToKey($array, $column, $keepOld = true)
    {
        $newArray = [];
        foreach($array as $row)
        {
            $key = $row[$column];
            if(!$keepOld)
            {
                unset($row[$column]);
            }
            $newArray[$key] = $row;
        }
        return $newArray;
    }

    /**
     * 判断数组是否为关联数组
     * @param array $array
     * @return bool
     */
    public static function isAssoc($array)
    {  
        return array_keys($array) !== range(0, count($array) - 1);  
    }

    /**
     * 随机获得数组中的值，返回一个保持键值对应的数组
     *
     * @param array $array
     * @param int $number
     * @return array
     */
    public static function random($array, $number = 1)
    {
        $result = [];
        $keys = array_rand($array, $number);
        foreach($keys as $key)
        {
            if(!isset($array[$key]))
            {
                break;
            }
            $result[$key] = $array[$key];
        }
        return $result;
    }

}