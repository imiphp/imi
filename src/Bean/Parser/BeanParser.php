<?php

declare(strict_types=1);

namespace Imi\Bean\Parser;

use Imi\Aop\Annotation\BaseInjectValue;
use Imi\Bean\BeanManager;

class BeanParser extends BaseParser
{
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
        if ($annotation instanceof \Imi\Bean\Annotation\Bean)
        {
            $beanName = $annotation->name ?? $className;
            BeanManager::add($className, $beanName, $annotation->instanceType);
        }
        elseif ($annotation instanceof BaseInjectValue)
        {
            BeanManager::addPropertyInject($className, $targetName, \get_class($annotation), $annotation->toArray());
        }
    }
}
