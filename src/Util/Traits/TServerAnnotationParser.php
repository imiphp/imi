<?php

declare(strict_types=1);

namespace Imi\Util\Traits;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Config;
use Imi\Util\Text;

/**
 * 注解处理器按服务器名获取.
 */
trait TServerAnnotationParser
{
    /**
     * 根据服务器获取的控制器缓存.
     *
     * @var array
     */
    private $cache = [];

    /**
     * 根据服务器获取对应的控制器数据.
     *
     * @param string $serverName
     *
     * @return array
     */
    public function getByServer($serverName)
    {
        $cache = &$this->cache;
        if (isset($cache[$serverName]))
        {
            return $cache[$serverName];
        }
        $namespaces = Config::get('@server.' . $serverName . '.beanScan', []);
        foreach ($namespaces as &$namespace)
        {
            if ('\\' !== substr($namespace, -1, 1))
            {
                $namespace .= '\\';
            }
        }
        unset($namespace);
        $result = [];
        foreach (AnnotationManager::getAnnotationPoints($this->controllerAnnotationClass, 'class') as $option)
        {
            $class = $option->getClass();
            foreach ($namespaces as $namespace)
            {
                if (Text::startwith($class, $namespace))
                {
                    $result[$class] = $option;
                    continue 2;
                }
            }
        }
        $cache[$serverName] = $result;

        return $result;
    }
}
