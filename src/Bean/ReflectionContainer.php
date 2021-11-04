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
     */
    private static array $classReflectionMap = [];

    /**
     * 方法反射集合.
     */
    private static array $methodReflectionMap = [];

    /**
     * 属性反射集合.
     */
    private static array $propertyReflectionMap = [];

    private function __construct()
    {
    }

    /**
     * 获取类反射.
     */
    public static function getClassReflection(string $className): ReflectionClass
    {
        return self::$classReflectionMap[$className] ?? (self::$classReflectionMap[$className] = new ReflectionClass($className));
    }

    /**
     * 获取方法反射.
     */
    public static function getMethodReflection(string $className, string $methodName): ReflectionMethod
    {
        return self::$methodReflectionMap[$className][$methodName] ?? (self::$methodReflectionMap[$className][$methodName] = static::getClassReflection($className)->getMethod($methodName));
    }

    /**
     * 获取属性反射.
     */
    public static function getPropertyReflection(string $className, string $propertyName): ReflectionProperty
    {
        return self::$propertyReflectionMap[$className][$propertyName] ?? (self::$propertyReflectionMap[$className][$propertyName] = static::getClassReflection($className)->getProperty($propertyName));
    }
}
