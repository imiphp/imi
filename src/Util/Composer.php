<?php

declare(strict_types=1);

namespace Imi\Util;

use Composer\Autoload\ClassLoader;
use Imi\Macro\AutoLoader;

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
                // @phpstan-ignore-next-line
                if ($autoloadFunction[0] instanceof AutoLoader)
                {
                    // @phpstan-ignore-next-line
                    $classLoaders[] = $autoloadFunction[0]->getComposerClassLoader();
                }
                else
                {
                    $classLoaders[] = $autoloadFunction[0];
                }
            }
        }

        return self::$classLoaders = $classLoaders;
    }

    /**
     * 获取路径对应的所有命名空间.
     */
    public static function getPathNamespaces(string $path): array
    {
        $realPath = realpath($path);
        if (!$realPath)
        {
            return [];
        }
        $result = [];
        foreach (self::getClassLoaders() as $classLoader)
        {
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
