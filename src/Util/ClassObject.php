<?php
namespace Imi\Util;

/**
 * 类和对象相关工具类
 */
abstract class ClassObject
{
    /**
     * 是否是匿名类对象
     * @param object|string $object
     * @return boolean
     */
    public static function isAnymous($object)
    {
        if(!is_string($object))
        {
            $object = get_class($object);
        }
        $index = strpos($object, 'class@anonymous');
        return false !== $index && $index >= 0;
    }

    /**
     * 处理可能是同级的类名
     * 如果 $className 是一个类名，则原样返回
     * 否则返回 $sameLevelClass 同级下的类名
     *
     * @param string $className
     * @param string $sameLevelClass
     * @return string
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

    /**
     * 将方法的参数处理成 kv 数组
     *
     * @param string $class
     * @param string $method
     * @param array $args
     * @param boolean $keepNotExistArgs 保留不存在的参数，如果保留则值则取默认值，没有默认值则为null
     * @return array
     */
    public static function convertArgsToKV($class, $method, $args, $keepNotExistArgs = true)
    {
        $methodRef = new \ReflectionMethod($class, $method);

        $result = [];
    
        foreach($methodRef->getParameters() as $i => $param)
        {
            if(isset($args[$i]))
            {
                $result[$param->name] = $args[$i];
            }
            else if($keepNotExistArgs)
            {
                $result[$param->name] = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
            }
            else
            {
                break;
            }
        }

        if(isset($param) && $param->isVariadic())
        {
            $result[$param->name] = [$result[$param->name]];
            if(isset($args[$i + 1]))
            {
                $count = count($args);
                for($i += 1; $i < $count; ++$i)
                {
                    $result[$param->name][] = $args[$i];
                }
            }
        }
    
        return $result;
    }

    /**
     * 获取一个类的所有子类
     *
     * @param string $class
     * @param string[] $classList
     * @return string[]
     */
    public static function getSubClasses($class, $classList = null)
    {
        $list = [];
        foreach($classList ?? get_declared_classes() as $tClass)
        {
            if(is_subclass_of($tClass, $class))
            {
                $list[] = $tClass;
            }
        }
        return $list;
    }

}