<?php

declare(strict_types=1);

namespace Imi\Main;

/**
 * 主类帮助类.
 */
class Helper
{
    private static array $mains = [];

    private static array $nameMap = [];

    private function __construct()
    {
    }

    /**
     * 获取主类实例对象
     *
     * @return \Imi\Main\BaseMain|null
     */
    public static function getMain(?string $namespace, ?string $componentName = null): ?BaseMain
    {
        if (null !== $namespace)
        {
            $mains = &static::$mains;
            if (null === $componentName)
            {
                // 获取
                if (isset($mains[$namespace]))
                {
                    return $mains[$namespace];
                }
                else
                {
                    return null;
                }
            }
            else
            {
                // 获取或新实例
                if (isset($mains[$namespace]))
                {
                    return $mains[$namespace];
                }
                else
                {
                    return static::newInstance($namespace, $componentName);
                }
            }
        }
        elseif (null !== $componentName)
        {
            $mains = &static::$mains;
            $nameMap = &static::$nameMap;
            if (!isset($nameMap[$componentName], $mains[$nameMap[$componentName]]))
            {
                return null;
            }

            return $mains[$nameMap[$componentName]];
        }
        else
        {
            return null;
        }
    }

    /**
     * 获取所有Main对象
     *
     * @return \Imi\Main\BaseMain[]
     */
    public static function getMains(): array
    {
        return static::$mains;
    }

    /**
     * 获取项目Main对象
     *
     * @return \Imi\Main\BaseMain[]
     */
    public static function getAppMains(): array
    {
        $mains = static::$mains;
        unset($mains['Imi']);

        return $mains;
    }

    private static function newInstance(string $namespace, string $componentName): ?BaseMain
    {
        $className = $namespace . '\\Main';
        if (class_exists($className))
        {
            static::$mains[$namespace] = $instance = new $className($componentName);
            static::$nameMap[$componentName] = $namespace;

            return $instance;
        }
        else
        {
            return null;
        }
    }
}
