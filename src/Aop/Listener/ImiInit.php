<?php

declare(strict_types=1);

namespace Imi\Aop\Listener;

use Imi\Aop\AopAnnotationLoader;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Main\Helper;

/**
 * @Listener(eventName="IMI.LOAD_RUNTIME",priority=19940290)
 */
class ImiInit implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(EventParam $e): void
    {
        // Aop 配置注入
        foreach (Helper::getMains() as $main)
        {
            $this->parseConfigs($main->getConfig()['aop'] ?? []);
        }
        AopAnnotationLoader::load(false);
    }

    /**
     * 处理配置.
     */
    private function parseConfigs(array $configs): void
    {
        if ($configs)
        {
            foreach ($configs as $className => $classConfig)
            {
                // 类
                AnnotationManager::addClassAnnotations($className, new \Imi\Aop\Annotation\Aspect());

                // 方法
                foreach ($classConfig['methods'] ?? [] as $methodName => $methodConfig)
                {
                    $annotations = [];
                    foreach ($methodConfig as $annotationName => $annotationArgs)
                    {
                        if (class_exists($annotationName))
                        {
                            $annotationClassName = $annotationName;
                        }
                        else
                        {
                            $annotationClassName = '\Imi\Aop\Annotation\\' . ucfirst($annotationName);
                        }
                        $annotations[] = new $annotationClassName($annotationArgs);
                    }
                    AnnotationManager::addMethodAnnotations($className, $methodName, ...$annotations);
                }

                // 属性
                foreach ($classConfig['properties'] ?? [] as $propName => $propConfig)
                {
                    $annotations = [];
                    foreach ($propConfig as $annotationName => $annotationArgs)
                    {
                        if (class_exists($annotationName))
                        {
                            $annotationClassName = $annotationName;
                        }
                        else
                        {
                            $annotationClassName = '\Imi\Aop\Annotation\\' . ucfirst($annotationName);
                        }
                        $annotations[] = new $annotationClassName($annotationArgs);
                    }
                    AnnotationManager::addPropertyAnnotations($className, $propName, ...$annotations);
                }
            }
        }
    }
}
