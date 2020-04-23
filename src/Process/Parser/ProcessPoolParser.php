<?php
namespace Imi\Process\Parser;

use Imi\Bean\Parser\BaseParser;
use Imi\Process\Annotation\ProcessPool;

class ProcessPoolParser extends BaseParser
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
        if($annotation instanceof ProcessPool)
        {
            $data = &$this->data;
            if(isset($data[$annotation->name]) && $data[$annotation->name]['className'] != $className)
            {
                throw new \RuntimeException(sprintf('Process pool %s is exists', $annotation->name));
            }
            $data[$annotation->name] = [
                'className'     => $className,
                'ProcessPool'   => $annotation,
            ];
        }
    }

    /**
     * 获取processPool信息
     * @param string $name processPool名称
     * @return array
     */
    public function getProcessPool($name)
    {
        return $this->data[$name] ?? null;
    }
}