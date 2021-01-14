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
     *
     * @param bool $statistics
     *
     * @return void
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
     *
     * @param bool $statistics
     *
     * @return void
     */
    public static function scanVendor(bool $statistics = true): void
    {
        $time = microtime(true);
        foreach (Composer::getClassLoaders() as $classLoader)
        {
            $ref = new ReflectionClass($classLoader);
            $vendorPath = \dirname($ref->getFileName(), 2);
            $namespaces = [];
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
                    foreach (Composer::getPathNamespaces($pathName) as $namespace)
                    {
                        if ('Imi\\' === $namespace)
                        {
                            continue;
                        }
                        $mainClassName = $namespace . 'Main';
                        // 判断是否合法的 imi Main 类
                        if (class_exists($mainClassName) && is_subclass_of($namespace, IMain::class))
                        {
                            // 此目录为 imi 组件目录
                            $namespaces[] = $namespace;
                            Helper::getMain($namespace, basename($pathName));
                            break;
                        }
                    }
                }
            }
        }
        foreach (Config::get('@app.components') as $name => $namespace)
        {
            $namespaces[] = $namespace;
            Helper::getMain($namespace, $name);
        }
        Annotation::getInstance()->initByNamespace($namespaces);
        Event::trigger('IMI.SCAN_VENDOR');
        if ($statistics)
        {
            $time = microtime(true) - $time;
            Log::info('scanVendor ' . $time . 's');
        }
    }

    /**
     * 扫描项目.
     *
     * @param bool $statistics
     *
     * @return void
     */
    public static function scanApp(bool $statistics = true): void
    {
        $time = microtime(true);
        $namespace = App::getNamespace();
        Helper::getMain($namespace, 'app');
        Annotation::getInstance()->initByNamespace($namespace);
        Event::trigger('IMI.SCAN_APP');
        if ($statistics)
        {
            $time = microtime(true) - $time;
            Log::info('scanApp ' . $time . 's');
        }
    }
}
