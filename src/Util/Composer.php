<?php

declare(strict_types=1);

namespace Imi\Util;

use Composer\Autoload\ClassLoader;

/**
 * Composer 工具类.
 */
class Composer
{
    private static ?array $classLoaders = null;

    private function __construct()
    {
    }

    /**
     * 获取 Composer ClassLoader 对象
     *
     * @return \Composer\Autoload\ClassLoader[]
     */
    public static function getClassLoaders(): array
    {
        if (null !== self::$classLoaders)
        {
            return self::$classLoaders;
        }
        $classLoaders = [];
        foreach (get_declared_classes() as $class)
        {
            if (Text::startwith($class, 'ComposerAutoloaderInit'))
            {
                $classLoaders[] = $class::getLoader();
            }
        }

        return self::$classLoaders = $classLoaders;
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
