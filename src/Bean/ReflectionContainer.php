<?php

declare(strict_types=1);

namespace Imi\Bean;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * 反射容器.
 */
class ReflectionContainer
{
    /**
     * 类反射集合.
     *
     * @var array
     */
    private static $classReflectionMap = [];

    /**
     * 方法反射集合.
     *
     * @var array
     */
    private static $methodReflectionMap = [];

    /**
     * 属性反射集合.
     *
     * @var array
     */
    private static $propertyReflectionMap = [];

    private function __construct()
    {
    }

    /**
     * 获取类反射.
     *
     * @param string $className
     *
     * @return \ReflectionClass
     */
    public static function getClassReflection(string $className): ReflectionClass
    {
        $map = &static::$classReflectionMap;
        if (isset($map[$className]))
        {
            return $map[$className];
        }
        else
        {
            return $map[$className] = new ReflectionClass($className);
        }
    }

    /**
     * 获取方法反射.
     *
     * @param string $className
     * @param string $methodName
     *
     * @return \ReflectionMethod
     */
    public static function getMethodReflection(string $className, string $methodName): ReflectionMethod
    {
        $map = &static::$methodReflectionMap;
        if (isset($map[$className][$methodName]))
        {
            return $map[$className][$methodName];
        }
        else
        {
            return $map[$className][$methodName] = static::getClassReflection($className)->getMethod($methodName);
        }
    }

    /**
     * 获取属性反射.
     *
     * @param string $className
     * @param string $propertyName
     *
     * @return \ReflectionProperty
     */
    public static function getPropertyReflection(string $className, string $propertyName): ReflectionProperty
    {
        $map = &static::$propertyReflectionMap;
        if (isset($map[$className][$propertyName]))
        {
            return $map[$className][$propertyName];
        }
        else
        {
            return $map[$className][$propertyName] = static::getClassReflection($className)->getProperty($propertyName);
        }
    }
}
