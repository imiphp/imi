<?php

declare(strict_types=1);

namespace Imi\Util;

use Imi\Bean\ReflectionContainer;

/**
 * 类和对象相关工具类.
 */
class ClassObject
{
    private function __construct()
    {
    }

    /**
     * 是否是匿名类对象
     *
     * @param object|string $object
     */
    public static function isAnymous($object): bool
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
        $methodRef = ReflectionContainer::getMethodReflection($class, $method);

        $result = [];

        foreach ($methodRef->getParameters() as $i => $param)
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

        return $namespace === substr($class, 0, \strlen($namespace));
    }
}
