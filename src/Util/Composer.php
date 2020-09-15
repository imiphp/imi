<?php

namespace Imi\Util;

use Composer\Autoload\ClassLoader;

/**
 * Composer 工具类.
 */
class Composer
{
    private static ClassLoader $classLoader;

    private function __construct()
    {
    }

    /**
     * 获取 Composer ClassLoader 对象
     *
     * @return \Composer\Autoload\ClassLoader|null
     */
    public static function getClassLoader(): ?ClassLoader
    {
        if (isset(self::$classLoader))
        {
            return self::$classLoader;
        }
        foreach (get_declared_classes() as $class)
        {
            if (Text::startwith($class, 'ComposerAutoloaderInit'))
            {
                return self::$classLoader = $class::getLoader();
            }
        }
    }

    /**
     * 获取路径对应的所有命名空间.
     *
     * @param string $path
     *
     * @return array
     */
    public static function getPathNamespaces(string $path): array
    {
        $classLoader = self::getClassLoader();
        $result = [];
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

        return $result;
    }
}
