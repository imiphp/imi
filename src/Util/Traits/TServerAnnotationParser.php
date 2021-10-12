<?php

declare(strict_types=1);

namespace Imi\Util\Traits;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Config;

/**
 * 注解处理器按服务器名获取.
 */
trait TServerAnnotationParser
{
    /**
     * 根据服务器获取的控制器缓存.
     */
    private array $cache = [];

    protected string $controllerAnnotationClass;

    /**
     * 根据服务器获取对应的控制器数据.
     */
    public function getByServer(string $serverName): array
    {
        $cache = &$this->cache;
        if (isset($cache[$serverName]))
        {
            return $cache[$serverName];
        }
        $namespaces = Config::get('@server.' . $serverName . '.beanScan', []);
        $result = [];
        if ($namespaces)
        {
            foreach ($namespaces as &$namespace)
            {
                if ('\\' !== ($namespace[-1] ?? ''))
                {
                    $namespace .= '\\';
                }
            }
            unset($namespace);
            foreach (AnnotationManager::getAnnotationPoints($this->controllerAnnotationClass, 'class') as $option)
            {
                $class = $option->getClass();
                foreach ($namespaces as $namespace)
                {
                    if (str_starts_with($class, $namespace))
                    {
                        $result[$class] = $option;
                        continue 2;
                    }
                }
            }
        }
        else
        {
            foreach (AnnotationManager::getAnnotationPoints($this->controllerAnnotationClass, 'class') as $option)
            {
                $result[$option->getClass()] = $option;
            }
        }

        return $cache[$serverName] = $result;
    }
}
