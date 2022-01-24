<?php

declare(strict_types=1);

namespace Imi\Util;

use Composer\Autoload\ClassLoader;
use Imi\Macro\AutoLoader;
use function array_search;

/**
 * Composer 工具类.
 */
class Composer
{
    private static ?array $classLoaders = null;

    private static ?array $classLoadersWithVendorDir = null;

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
            if (\is_array($autoloadFunction) && isset($autoloadFunction[0]))
            {
                // @phpstan-ignore-next-line
                if ($autoloadFunction[0] instanceof AutoLoader)
                {
                    // @phpstan-ignore-next-line
                    $classLoaders[] = $autoloadFunction[0]->getComposerClassLoader();
                }
                elseif ($autoloadFunction[0] instanceof ClassLoader)
                {
                    $classLoaders[] = $autoloadFunction[0];
                }
            }
        }

        return self::$classLoaders = $classLoaders;
    }

    /**
     * 获取 Composer ClassLoader 对象并携带 vendorDir
     * 仅 composer version >= 2.0.9 下效.
     *
     * @return array<string, \Composer\Autoload\ClassLoader>
     */
    public static function getClassLoadersWithVendorDir(bool $force = false): array
    {
        if (!$force && null !== self::$classLoadersWithVendorDir)
        {
            return self::$classLoadersWithVendorDir;
        }
        $classLoaders = [];

        // composer version >= 2.0.9
        if (method_exists(ClassLoader::class, 'getRegisteredLoaders'))
        {
            $loaderObjectsVendorDirs = [];
            foreach (ClassLoader::getRegisteredLoaders() as $vendorDir => $loader) {
                $loaderObjectsVendorDirs[\spl_object_id($loader)] = $vendorDir;
            }
            foreach (self::getClassLoaders($force) as $loader) {
                $objId = \spl_object_id($loader);
                if (isset($loaderObjectsVendorDirs[$objId])) {
                    $classLoaders[$loaderObjectsVendorDirs[$objId]] = $loader;
                }
            }
        }

        return self::$classLoadersWithVendorDir = $classLoaders;
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
