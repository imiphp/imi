<?php

declare(strict_types=1);

namespace Imi\Bean;

use Imi\Main\Helper as MainHelper;
use Imi\Util\File;
use Imi\Util\Imi;

/**
 * 注解加载类.
 */
class AnnotationLoader
{
    private array $loaded = [];

    /**
     * 加载模块注解.
     */
    public function loadModuleAnnotations(string $namespace, callable $callback, string $pattern): void
    {
        $loaded = &$this->loaded;
        // 避免重复加载
        if (isset($loaded[$namespace]))
        {
            return;
        }
        // 尝试当作模块获取主类
        $main = MainHelper::getMain($namespace);
        $namespacePaths = Imi::getNamespacePaths($namespace);
        if (null === $main)
        {
            $beanScan = [];
            foreach ($namespacePaths as $path)
            {
                $configFileName = File::path($path, 'config/config.php');
                if (!is_file($configFileName))
                {
                    continue;
                }
                $data = include $configFileName;
                if (isset($data['beanScan']))
                {
                    $beanScan[] = $data['beanScan'];
                }
            }
            if (isset($beanScan[1]))
            {
                $beanScan = array_merge(...$beanScan);
            }
            elseif ($beanScan)
            {
                $beanScan = $beanScan[0];
            }
        }
        else
        {
            // 有主类
            $loaded[$namespace] = $main->getPath();
            // 遍历加载
            $beanScan = $main->getBeanScan();
        }
        if ($beanScan)
        {
            foreach ($beanScan as $namespace)
            {
                $this->loadModuleAnnotations($namespace, $callback, $pattern);
            }
        }
        foreach ($namespacePaths as $path)
        {
            $pathLength = \strlen($path);
            foreach (File::enumFile($path, $pattern, ['php']) as $file)
            {
                $path = $file->getFullPath();
                $diffPath = substr($path, $pathLength);
                if (isset($diffPath[0]) && \DIRECTORY_SEPARATOR === $diffPath[0])
                {
                    $diffPath = substr($diffPath, 1);
                }
                $diffPath = \dirname($diffPath);
                if ('.' === $diffPath)
                {
                    $diffPath = '';
                }
                else
                {
                    $diffPath .= \DIRECTORY_SEPARATOR;
                }
                $fileNamespace = $namespace . '\\' . str_replace(\DIRECTORY_SEPARATOR, '\\', $diffPath . basename($path, '.php'));
                // 回调
                $callback($fileNamespace, $path);
            }
        }
    }

    /**
     * 命名空间是否已加载.
     */
    public function isLoaded(string $namespace): bool
    {
        return isset($this->loaded[$namespace]);
    }
}
