<?php
namespace Imi\Enum\Annotation\Parser;

use Imi\Bean\Parser\BaseParser;
use Imi\Enum\Annotation\EnumItem;

class EnumParser extends BaseParser
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
        if($annotation instanceof EnumItem)
        {
            $value = constant($className . '::' . $targetName);
            $this->data['names'][$className][] = $targetName;
            $this->data['EnumItem'][$className][$value] = $annotation;
        }
    }

    /**
     * 获得常量文本
     *
     * @param string $className
     * @param mixed $value
     * @return string
     */
    public function getEnumItem($className, $value)
    {
        return $this->data['EnumItem'][$className][$value] ?? null;
    }

    /**
     * 获取所有名称
     *
     * @param string $className
     * @return string[]
     */
    public function getNames($className)
    {
        return $this->data['names'][$className] ?? [];
    }

    /**
     * 获取所有值
     *
     * @param string $className
     * @return array
     */
    public function getValues($className)
    {
        if(isset($this->data['EnumItem'][$className]))
        {
            return array_keys($this->data['EnumItem'][$className]);
        }
        else
        {
            return [];
        }
    }
}