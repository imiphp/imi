<?php

namespace Imi\Rpc\Route\Annotation\Parser;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\Parser\BaseParser;
use Imi\Config;
use Imi\Event\Event;
use Imi\Util\Text;

/**
 * 控制器注解处理器.
 */
class RpcControllerParser extends BaseParser
{
    /**
     * @var array
     */
    protected $cache = [];

    /**
     * 处理方法.
     *
     * @param \Imi\Bean\Annotation\Base $annotation 注解类
     * @param string                    $className  类名
     * @param string                    $target     注解目标类型（类/属性/方法）
     * @param string                    $targetName 注解目标名称
     *
     * @return void
     */
    public function parse(\Imi\Bean\Annotation\Base $annotation, string $className, string $target, string $targetName)
    {
        $eventName = 'IMI.RPC.ANNOTATION.PARSER:' . \get_class($annotation);
        Event::trigger($eventName, compact('annotation', 'className', 'target', 'targetName'), $this);
    }

    /**
     * 根据服务器获取对应的控制器数据.
     *
     * @param string $serverName
     * @param string $controllerAnnotationClass
     *
     * @return array
     */
    public function getByServer($serverName, $controllerAnnotationClass)
    {
        if (isset($this->cache[$serverName]))
        {
            return $this->cache[$serverName];
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
        foreach (AnnotationManager::getAnnotationPoints($controllerAnnotationClass, 'class') as $option)
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
        $this->cache[$serverName] = $result;

        return $result;
    }
}
