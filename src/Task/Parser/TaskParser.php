<?php
namespace Imi\Task\Parser;

use Imi\Task\Annotation\Task;
use Imi\Bean\Parser\BaseParser;

class TaskParser extends BaseParser
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
        if($annotation instanceof Task)
        {
            if(isset($this->data[$annotation->name]) && $this->data[$annotation->name]['className'] != $className)
            {
                throw new \RuntimeException(sprintf('Task %s is exists', $annotation->name));
            }
            $this->data[$annotation->name] = [
                'className' => $className,
                'Task'      => $annotation,
            ];
        }
    }

    /**
     * 获取task信息
     * @param string $name task名称
     * @return array
     */
    public function getTask($name)
    {
        return $this->data[$name] ?? null;
    }
}