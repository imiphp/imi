<?php
namespace Imi\Util;

/**
 * 类和对象相关工具类
 */
abstract class ClassObject
{
    /**
     * 是否是匿名类对象
     * @param object $object
     * @return boolean
     */
    public static function isAnymous($object)
    {
        $index = strpos(get_class($object), 'class@anonymous');
        return false !== $index && $index >= 0;
    }
}