<?php

declare(strict_types=1);

namespace Imi\Rpc\Route\Annotation\Parser;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\Parser\BaseParser;
use Imi\Config;
use Imi\Event\Event;
use Imi\Rpc\Route\Event\RpcAnnotationParserEvent;

/**
 * 控制器注解处理器.
 */
class RpcControllerParser extends BaseParser
{
    protected array $cache = [];

    /**
     * {@inheritDoc}
     */
    public function parse(\Imi\Bean\Annotation\Base $annotation, string $className, string $target, string $targetName): void
    {
        $eventName = 'imi.rpc.annotation.parser:' . $annotation::class;
        Event::dispatch(new RpcAnnotationParserEvent($eventName, $this, $annotation, $className, $target, $targetName));
    }

    /**
     * 根据服务器获取对应的控制器数据.
     */
    public function getByServer(string $serverName, string $controllerAnnotationClass): array
    {
        if (isset($this->cache[$serverName]))
        {
            return $this->cache[$serverName];
        }
        $namespaces = Config::get('@server.' . $serverName . '.beanScan', []);
        foreach ($namespaces as &$namespace)
        {
            if (!str_ends_with((string) $namespace, '\\'))
            {
                $namespace .= '\\';
            }
        }
        unset($namespace);
        $result = [];
        foreach (AnnotationManager::getAnnotationPoints($controllerAnnotationClass, 'class') as $option)
        {
            $class = $option->getClass();
            foreach ($namespaces as $namespace)
            {
                if (str_starts_with($class, (string) $namespace))
                {
                    $result[$class] = $option;
                    continue 2;
                }
            }
        }

        return $this->cache[$serverName] = $result;
    }
}
