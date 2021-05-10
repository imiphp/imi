<?php

namespace Imi\Util;

use Composer\Autoload\ClassLoader;

/**
 * Composer 工具类.
 */
abstract class Composer
{
    /**
     * @var array|null
     */
    private static $classLoaders = null;

    /**
     * 获取 Composer ClassLoader 对象
     *
     * @return \Composer\Autoload\ClassLoader|null
     */
    public static function getClassLoader(): ?ClassLoader
    {
        return self::getClassLoaders()[0] ?? null;
    }

    /**
     * 获取 Composer ClassLoader 对象
     *
     * @return \Composer\Autoload\ClassLoader[]
     */
    public static function getClassLoaders(bool $force = false): array
    {
        if (!$force && null !== self::$classLoaders)
        {
            return self::$classLoaders;
        }
        $classLoaders = [];
        foreach (spl_autoload_functions() as $autoloadFunction)
        {
            if (\is_array($autoloadFunction) && isset($autoloadFunction[0]) && $autoloadFunction[0] instanceof ClassLoader)
            {
                $classLoaders[] = $autoloadFunction[0];
            }
        }

        return self::$classLoaders = $classLoaders;
    }

    /**
     * 获取路径对应的所有命名空间.
     */
    public static function getPathNamespaces(string $path): array
    {
        $result = [];
        foreach (self::getClassLoaders() as $classLoader)
        {
            $realPath = realpath($path);
            foreach ($classLoader->getPrefixesPsr4() as $namespace => $namespacePaths)
            {
                foreach ($namespacePaths as $namespacePath)
                {
                    if ($realPath === realpath($namespacePath))
                    {
                        $result[] = $namespace;
                    }
                }
            }
        }

        return $result;
    }
}
