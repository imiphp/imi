<?php

namespace Imi\Util;

use Imi\Bean\ReflectionContainer;
use InvalidArgumentException;
use ReflectionParameter;

/**
 * 类和对象相关工具类.
 */
abstract class ClassObject
{
    /**
     * 是否是匿名类对象
     *
     * @param object|string $object
     *
     * @return bool
     */
    public static function isAnymous($object)
    {
        if (!\is_string($object))
        {
            $object = \get_class($object);
        }
        $index = strpos($object, 'class@anonymous');

        // @phpstan-ignore-next-line
        return false !== $index && $index >= 0;
    }

    /**
     * 处理可能是同级的类名
     * 如果 $className 是一个类名，则原样返回
     * 否则返回 $sameLevelClass 同级下的类名.
     *
     * @param string $className
     * @param string $sameLevelClass
     *
     * @return string
     */
    public static function parseSameLevelClassName($className, $sameLevelClass)
    {
        if (class_exists($className))
        {
            return $className;
        }
        else
        {
            return Imi::getClassNamespace($sameLevelClass) . '\\' . $className;
        }
    }

    /**
     * 将方法的参数处理成 kv 数组.
     *
     * @param string $class
     * @param string $method
     * @param array  $args
     * @param bool   $keepNotExistArgs 保留不存在的参数，如果保留则值则取默认值，没有默认值则为null
     *
     * @return array
     */
    public static function convertArgsToKV(string $class, string $method, array $args, bool $keepNotExistArgs = true): array
    {
        if ('__construct' === $method)
        {
            $params = ReflectionContainer::getClassReflection($class)->getConstructor()->getParameters();
        }
        else
        {
            $params = ReflectionContainer::getMethodReflection($class, $method)->getParameters();
        }

        return self::convertArrayToKV($params, $args, $keepNotExistArgs);
    }

    /**
     * 将数组参数处理成 kv 数组.
     *
     * @param ReflectionParameter[] $params
     * @param bool                  $keepNotExistArgs 保留不存在的参数，如果保留则值则取默认值，没有默认值则为null
     */
    public static function convertArrayToKV(array $params, array $args, bool $keepNotExistArgs = true): array
    {
        $result = [];

        foreach ($params as $i => $param)
        {
            if (isset($args[$i]))
            {
                $result[$param->name] = $args[$i];
            }
            elseif ($keepNotExistArgs)
            {
                $result[$param->name] = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
            }
            else
            {
                break;
            }
        }

        if (isset($param) && $param->isVariadic())
        {
            $paramName = $param->name;
            $resultItem = [$result[$paramName]];
            // @phpstan-ignore-next-line
            if (isset($args[$i + 1]))
            {
                $count = \count($args);
                // @phpstan-ignore-next-line
                for (++$i; $i < $count; ++$i)
                {
                    $resultItem[] = $args[$i];
                }
            }
            $result[$paramName] = $resultItem;
        }

        return $result;
    }

    /**
     * 将 kv 数组处理成数组.
     */
    public static function convertKVToArray(array $params, array $args): array
    {
        $result = [];
        foreach ($params as $param)
        {
            $name = $param->getName();
            if (isset($args[$name]))
            {
                $result[] = $args[$name];
            }
            elseif ($param->isDefaultValueAvailable())
            {
                $result[] = $param->getDefaultValue();
            }
            else
            {
                $declaringClass = $param->getDeclaringClass();
                throw new InvalidArgumentException(sprintf('%s::__construct() %s not found', $declaringClass ? $declaringClass->getName() : '', $name));
            }
        }

        return $result;
    }

    /**
     * 获取一个类的所有子类.
     *
     * @param string   $class
     * @param string[] $classList
     *
     * @return string[]
     */
    public static function getSubClasses($class, $classList = null)
    {
        $list = [];
        foreach ($classList ?? get_declared_classes() as $tClass)
        {
            if (is_subclass_of($tClass, $class))
            {
                $list[] = $tClass;
            }
        }

        return $list;
    }

    /**
     * 判断某类是否在指定命名空间下.
     *
     * @param string $namespace
     * @param string $class
     *
     * @return bool
     */
    public static function inNamespace($namespace, $class)
    {
        if ('' !== $namespace && '\\' !== substr($namespace, -1, 1))
        {
            $namespace .= '\\';
        }

        return $namespace === substr($class, 0, \strlen($namespace));
    }

    /**
     * 增强实例化.
     */
    public static function newInstance(string $class, array $args): object
    {
        $constructor = ReflectionContainer::getClassReflection($class)->getConstructor();
        if (!$constructor)
        {
            return new $class();
        }

        return new $class(...self::convertKVToArray($constructor->getParameters(), $args));
    }
}
