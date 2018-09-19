<?php
namespace Imi\Aop\Listener;

use Imi\Main\Helper;
use Imi\Event\EventParam;
use Imi\Aop\Parser\AopParser;
use Imi\Event\IEventListener;
use Imi\Bean\Annotation\Listener;

/**
 * @Listener(eventName="IMI.INITED",priority=PHP_INT_MAX)
 */
class ImiInit implements IEventListener
{
    /**
     * 事件处理方法
     * @param EventParam $e
     * @return void
     */
    public function handle(EventParam $e)
    {
        // Aop 配置注入
        foreach(Helper::getMains() as $main)
        {
            $this->parseConfigs($main->getConfig()['aop'] ?? []);
        }
    }

    /**
     * 处理配置
     * @param array $configs
     * @return void
     */
    private function parseConfigs($configs)
    {
        $aopParser = AopParser::getInstance();
        foreach($configs as $className => $classConfig)
        {
            // 类
            $aopParser->parse(new \Imi\Aop\Annotation\Aspect(), $className, AopParser::TARGET_CLASS, $className);
            // 方法
            foreach($classConfig['methods'] ?? [] as $methodName => $methodConfig)
            {
                foreach($methodConfig as $annotationName => $annotationArgs)
                {
                    if(class_exists($annotationName))
                    {
                        $annotationClassName = $annotationName;
                    }
                    else
                    {
                        $annotationClassName = '\Imi\Aop\Annotation\\' . ucfirst($annotationName);
                    }
                    $aopParser->parse(new $annotationClassName($annotationArgs), $className, AopParser::TARGET_METHOD, $methodName);
                }
            }
            // 属性
            foreach($classConfig['properties'] ?? [] as $propName => $propConfig)
            {
                foreach($propConfig as $annotationName => $annotationArgs)
                {
                    if(class_exists($annotationName))
                    {
                        $annotationClassName = $annotationName;
                    }
                    else
                    {
                        $annotationClassName = '\Imi\Aop\Annotation\\' . ucfirst($annotationName);
                    }
                    $aopParser->parse(new $annotationClassName($annotationArgs), $className, AopParser::TARGET_PROPERTY, $propName);
                }
            }
        }
    }
}