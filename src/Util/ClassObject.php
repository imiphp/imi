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

    /**
     * 处理可能是同级的类名
     * 如果 $className 是一个类名，则原样返回
     * 否则返回 $sameLevelClass 同级下的类名
     *
     * @param string $className
     * @param string $sameLevelClass
     * @return void
     */
    public static function parseSameLevelClassName($className, $sameLevelClass)
    {
        if(class_exists($className))
        {
            return $className;
        }
        else
        {
            return Imi::getClassNamespace($sameLevelClass) . '\\' . $className;
        }
    }
}