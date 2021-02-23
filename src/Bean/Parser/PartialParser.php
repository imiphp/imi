<?php

declare(strict_types=1);

namespace Imi\Bean\Parser;

use Imi\Bean\PartialManager;

class PartialParser extends BaseParser
{
    /**
     * 处理方法.
     *
     * @param \Imi\Bean\Annotation\Partial $annotation 注解类
     * @param string                       $className  类名
     * @param string                       $target     注解目标类型（类/属性/方法）
     * @param string                       $targetName 注解目标名称
     *
     * @return void
     */
    public function parse(\Imi\Bean\Annotation\Base $annotation, string $className, string $target, string $targetName)
    {
        // Partial 仅支持定义为 trait
        if (!trait_exists($className, true))
        {
            return;
        }
        PartialManager::add($className, $annotation->class);
    }
}
