<?php
namespace Imi\Model\Parser;

use Imi\Bean\Parser\BaseParser;
use Imi\Model\Annotation\Table;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\MemoryTable;
use Imi\Model\Annotation\RedisEntity;
use Imi\Model\Annotation\Serializable;
use Imi\Model\Annotation\Serializables;
use Imi\Model\Annotation\ExtractProperty;

class ModelParser extends BaseParser
{
    /**
     * 处理方法
     * @param \Imi\Bean\Annotation\Base $annotation 注解类
     * @param string $className 类名
     * @param string $target 注解目标类型（类/属性/方法）
     * @param string $targetName 注解目标名称
     * @return void
     */
    public function parse(\Imi\Bean\Annotation\Base $annotation, string $className, string $target, string $targetName)
    {
        
    }
}