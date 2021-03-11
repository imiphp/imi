<?php

namespace Imi\Bean;

use Imi\Main\Helper as MainHelper;
use Imi\Util\File;
use Imi\Util\Imi;

/**
 * 注解加载类.
 */
class AnnotationLoader
{
    /**
     * @var array
     */
    private $loaded = [];

    /**
     * 加载模块注解.
     *
     * @param string   $namespace
     * @param callable $callback
     *
     * @return void
     */
    public function loadModuleAnnotations($namespace, $callback)
    {
        $this->enumModulePath($namespace, function ($ns, $path) use ($callback) {
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
                $fileNamespace = $ns . '\\' . str_replace(\DIRECTORY_SEPARATOR, '\\', $diffPath . basename($filePath, '.php'));
                // 回调
                $callback($fileNamespace);
            }
        });
    }

    /**
     * 遍历模块目录，并调用回调.
     *
     * @param string   $namespace
     * @param callable $callback
     *
     * @return void
     */
    public function enumModulePath($namespace, $callback)
    {
        $loaded = &$this->loaded;
        // 避免重复加载
        if (isset($loaded[$namespace]))
        {
            return;
        }
        // 尝试当作模块获取主类
        $main = MainHelper::getMain($namespace);
        if (null === $main)
        {
            // 无主类
            $namespaceSplit = explode('\\', $namespace);
            $pops = [];
            // 命名空间一级一级往上尝试
            do
            {
                $pops[] = array_pop($namespaceSplit);
                $tNamespace = implode('\\', $namespaceSplit);
                if (isset($loaded[$tNamespace]))
                {
                    $paths = (array) $loaded[$tNamespace];
                }
                else
                {
                    $paths = Imi::getNamespacePaths($tNamespace);
                    if (!$paths)
                    {
                        continue;
                    }
                }
                foreach ($paths as $path)
                {
                    $path .= \DIRECTORY_SEPARATOR . implode(\DIRECTORY_SEPARATOR, array_reverse($pops));
                    $callback($namespace, $path);
                }
                break;
            } while (isset($namespaceSplit[0]));
            // 未能成功加载抛出异常
            if (!isset($namespaceSplit[0]))
            {
                throw new \RuntimeException('Can not load annotations: ' . $namespace);
            }
        }
        else
        {
            // 有主类
            $loaded[$main->getNamespace()] = $main->getPath();
            // 遍历加载
            foreach ($main->getBeanScan() as $namespace)
            {
                $this->enumModulePath($namespace, $callback);
            }
        }
    }
}
