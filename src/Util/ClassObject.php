<?php

declare(strict_types=1);

namespace Imi\Util;

use Imi\Bean\ReflectionContainer;

/**
 * 类和对象相关工具类.
 */
class ClassObject
{
    use \Imi\Util\Traits\TStaticClass;

    /**
     * 是否是匿名类对象
     */
    public static function isAnymous(object|string $object): bool
    {
        if (!\is_string($object))
        {
            $object = $object::class;
        }

        return str_contains($object, 'class@anonymous');
    }

    /**
     * 处理可能是同级的类名
     * 如果 $className 是一个类名，则原样返回
     * 否则返回 $sameLevelClass 同级下的类名.
     */
    public static function parseSameLevelClassName(string $className, string $sameLevelClass): string
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
     * @param bool $keepNotExistArgs 保留不存在的参数，如果保留则值则取默认值，没有默认值则为null
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
     * @param \ReflectionParameter[] $params
     * @param bool                   $keepNotExistArgs 保留不存在的参数，如果保留则值则取默认值，没有默认值则为null
     */
    public static function convertArrayToKV(array $params, array $args, bool $keepNotExistArgs = true): array
    {
        if (!$params)
        {
            return [];
        }
        $result = [];

        foreach ($params as $i => $param)
        {
            if (\array_key_exists($i, $args))
            {
                $result[$param->name] = &$args[$i];
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

        if ($param->isVariadic())
        {
            $paramName = $param->name;
            $resultItem = [$result[$paramName]];
            if (\array_key_exists($i + 1, $args))
            {
                $count = \count($args);
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
        if (!$params)
        {
            return [];
        }
        $result = [];
        foreach ($params as $param)
        {
            $name = $param->getName();
            if (\array_key_exists($name, $args))
            {
                $result[] = &$args[$name];
            }
            elseif ($param->isDefaultValueAvailable())
            {
                $result[] = $param->getDefaultValue();
            }
            else
            {
                $declaringClass = $param->getDeclaringClass();
                throw new \InvalidArgumentException(sprintf('Argument %s of %s::__construct() does not found', $name, $declaringClass ? $declaringClass->getName() : ''));
            }
        }

        return $result;
    }

    /**
     * 获取一个类的所有子类.
     *
     * @param string[] $classList
     *
     * @return string[]
     */
    public static function getSubClasses(string $class, ?array $classList = null): array
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
     */
    public static function inNamespace(string $namespace, string $class): bool
    {
        if ('' !== $namespace && '\\' !== $namespace[-1])
        {
            $namespace .= '\\';
        }

        return str_starts_with($class, $namespace);
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

    /**
     * 获取对象公开属性值
     */
    public static function getPublicProperties(?object $object): array
    {
        // 套一层是为了避免 get_object_vars() 作用域问题，可以只返回 public 属性
        return get_object_vars($object);
    }
}
