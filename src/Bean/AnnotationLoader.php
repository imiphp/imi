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
    public function loadModuleAnnotations(string $namespace, callable $callback): void
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
                $this->loadModuleAnnotations($namespace, $callback);
            }
        }
        foreach ($namespacePaths as $path)
        {
            $pathLength = \strlen($path);
            foreach (File::enumPHPFile($path) as $filePath)
            {
                $filePath = $filePath[0];
                $diffPath = substr($filePath, $pathLength);
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
                $fileNamespace = $namespace . '\\' . str_replace(\DIRECTORY_SEPARATOR, '\\', $diffPath . basename($filePath, '.php'));
                // 回调
                $callback($fileNamespace);
            }
        }
    }
}
