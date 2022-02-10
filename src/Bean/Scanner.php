<?php

declare(strict_types=1);

namespace Imi\Bean;

use FilesystemIterator;
use Imi\App;
use Imi\Config;
use Imi\Core\Component\ComponentManager;
use Imi\Event\Event;
use Imi\Log\Log;
use Imi\Main\Helper;
use Imi\Main\IMain;
use Imi\Util\Composer;
use Imi\Util\File;
use Imi\Util\Imi;
use ReflectionClass;
use function sprintf;

/**
 * 扫描类.
 */
class Scanner
{
    private function __construct()
    {
    }

    /**
     * 扫描 imi 框架.
     */
    public static function scanImi(bool $statistics = true): void
    {
        $time = microtime(true);
        Helper::getMain('Imi', 'Imi');
        Annotation::getInstance()->initByNamespace('Imi');
        Event::trigger('IMI.SCAN_IMI');
        if ($statistics)
        {
            $time = microtime(true) - $time;
            Log::info(sprintf('scanImi %.3fs', $time));
        }
    }

    /**
     * 扫描 vendor 目录中的组件.
     */
    public static function scanVendor(bool $statistics = true): void
    {
        $time = microtime(true);
        $components = [];
        $fileNameMap = [];
        $classLoaders = Composer::getClassLoadersWithVendorDir() ?: Composer::getClassLoaders();
        foreach ($classLoaders as $vendorDir => $classLoader)
        {
            if (\is_string($vendorDir) && is_dir($vendorDir))
            {
                if (isset($fileNameMap[$vendorDir]))
                {
                    continue;
                }
                $fileNameMap[$vendorDir] = true;
                $vendorPath = $vendorDir;
            }
            else
            {
                // 兼容无法直接获取 vendorDir 的情况，但无法保证多个 loader 存在的情况下获取到正确的目录
                $ref = new ReflectionClass($classLoader);
                $fileName = $ref->getFileName();
                if (isset($fileNameMap[$fileName]))
                {
                    continue;
                }
                $fileNameMap[$fileName] = true;
                $vendorPath = \dirname($fileName, 2);
            }
            // 遍历第一层
            var_dump("vendorPath $vendorPath");
            foreach (new FilesystemIterator($vendorPath, FilesystemIterator::SKIP_DOTS) as $dir1)
            {
                if (!$dir1->isDir())
                {
                    continue;
                }
                // 遍历第二层
                foreach (new FilesystemIterator($dir1->getPathname(), FilesystemIterator::SKIP_DOTS) as $dir2)
                {
                    if (!$dir2->isDir())
                    {
                        continue;
                    }
                    $pathName = $dir2->getPathname();
                    // 有 composer.json 的目录才是包目录
                    if (!is_file(File::path($pathName, 'composer.json')))
                    {
                        continue;
                    }
                    foreach (Composer::getPathNamespaces($pathName . '/src') as $namespace)
                    {
                        if ('Imi\\' === $namespace)
                        {
                            continue;
                        }
                        $mainClassName = $namespace . 'Main';
                        // 判断是否合法的 imi Main 类
                        if (class_exists($mainClassName) && is_subclass_of($mainClassName, IMain::class))
                        {
                            // 此目录为 imi 组件目录
                            $realNamespace = rtrim($namespace, '\\');
                            $componentName = basename($pathName);
                            $components[$componentName] = $realNamespace;
                            break;
                        }
                    }
                }
            }
        }
        var_dump('imi components');
        var_dump($components);
        $components = array_unique(array_merge(Config::get('@app.components', []), $components));
        if ($components)
        {
            self::scanComponents($components);
        }
        Event::trigger('IMI.SCAN_VENDOR');
        if ($statistics)
        {
            $time = microtime(true) - $time;
            Log::info(sprintf('scanVendor %.3fs', $time));
        }
    }

    /**
     * 扫描项目.
     */
    public static function scanApp(bool $statistics = true): void
    {
        $time = microtime(true);
        $namespace = App::getNamespace();
        Helper::getMain($namespace, 'app');
        Annotation::getInstance()->initByNamespace($namespace, true);
        Event::trigger('IMI.SCAN_APP');
        if ($statistics)
        {
            $time = microtime(true) - $time;
            Log::info(sprintf('scanApp %.3fs', $time));
        }
    }

    /**
     * 扫描组件.
     */
    public static function scanComponents(array $components): void
    {
        $annotation = Annotation::getInstance();
        $loader = $annotation->getLoader();
        $namespaces = [];
        foreach ($components as $name => $namespace)
        {
            if (!$loader->isLoaded($namespace))
            {
                $namespaces[] = $namespace;
            }
            if (!ComponentManager::has($name))
            {
                ComponentManager::addComponent($name, $namespace);
            }
        }
        $annotation->initByNamespace($namespaces);
        $nextComponents = [];
        foreach ($namespaces as $namespace)
        {
            var_dump("scanComponents $namespace");
            foreach (Imi::getNamespacePaths($namespace) as $path)
            {
                var_dump("path $path/config/config.php");
                $fileName = $path . '/config/config.php';
                if (is_file($fileName))
                {
                    $config = include $fileName;
                    $configComponents = $config['components'] ?? null;
                    if ($configComponents)
                    {
                        $nextComponents = array_merge($nextComponents, $configComponents);
                    }
                    break;
                }
            }
        }
        if ($nextComponents && ($nextComponents = array_diff($nextComponents, $components)))
        {
            self::scanComponents($nextComponents);
        }
    }
}
