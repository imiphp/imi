<?php

declare(strict_types=1);

namespace Imi\Bean;

use FilesystemIterator;
use Imi\App;
use Imi\Config;
use Imi\Event\Event;
use Imi\Log\Log;
use Imi\Main\Helper;
use Imi\Main\IMain;
use Imi\Util\Composer;
use Imi\Util\File;
use Imi\Util\Imi;
use ReflectionClass;

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
            Log::info('scanImi ' . $time . 's');
        }
    }

    /**
     * 扫描 vendor 目录中的组件.
     */
    public static function scanVendor(bool $statistics = true): void
    {
        $time = microtime(true);
        $components = [];
        foreach (Composer::getClassLoaders() as $classLoader)
        {
            $ref = new ReflectionClass($classLoader);
            $vendorPath = \dirname($ref->getFileName(), 2);
            // 遍历第一层
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
                            Helper::getMain($realNamespace, $componentName);
                            break;
                        }
                    }
                }
            }
        }
        $components = array_merge($components, Config::get('@app.components', []));
        if ($components)
        {
            self::scanComponents($components);
        }
        Event::trigger('IMI.SCAN_VENDOR');
        if ($statistics)
        {
            $time = microtime(true) - $time;
            Log::info('scanVendor ' . $time . 's');
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
            Log::info('scanApp ' . $time . 's');
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
                Helper::getMain($namespace, $name);
            }
        }
        $annotation->initByNamespace($namespaces);
        $nextComponents = [];
        foreach ($namespaces as $namespace)
        {
            foreach (Imi::getNamespacePaths($namespace) as $path)
            {
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
        if ($nextComponents)
        {
            self::scanComponents($nextComponents);
        }
    }
}
