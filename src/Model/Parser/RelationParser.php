<?php
namespace Imi\Model\Parser;

use Imi\Bean\Parser\BaseParser;
use Imi\Model\Annotation\Relation\JoinTo;
use Imi\Model\Annotation\Relation\AutoSave;
use Imi\Model\Annotation\Relation\JoinFrom;
use Imi\Model\Annotation\Relation\OneToOne;
use Imi\Model\Annotation\Relation\OneToMany;
use Imi\Model\Annotation\Relation\AutoDelete;
use Imi\Model\Annotation\Relation\AutoInsert;
use Imi\Model\Annotation\Relation\AutoSelect;
use Imi\Model\Annotation\Relation\AutoUpdate;
use Imi\Model\Annotation\Relation\ManyToMany;
use Imi\Model\Annotation\Relation\JoinToMiddle;
use Imi\Model\Annotation\Relation\JoinFromMiddle;
use Imi\Model\Annotation\Relation\PolymorphicToOne;
use Imi\Model\Annotation\Relation\PolymorphicOneToOne;
use Imi\Model\Annotation\Relation\PolymorphicOneToMany;
use Imi\Model\Annotation\Relation\PolymorphicManyToMany;


class RelationParser extends BaseParser
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
        if($annotation instanceof OneToOne || $annotation instanceof OneToMany || $annotation instanceof ManyToMany || $annotation instanceof PolymorphicOneToOne || $annotation instanceof PolymorphicOneToMany || $annotation instanceof PolymorphicManyToMany)
        {
            $this->data[$className]['relations'][$targetName] = $annotation;
        }
        else if($annotation instanceof PolymorphicToOne)
        {
            $this->data[$className]['relations'][$targetName][] = $annotation;
        }
        else if($annotation instanceof JoinFrom)
        {
            $this->data[$className]['properties'][$targetName]['JoinFrom'] = $annotation;
        }
        else if($annotation instanceof JoinTo)
        {
            $this->data[$className]['properties'][$targetName]['JoinTo'] = $annotation;
        }
        else if($annotation instanceof JoinFromMiddle)
        {
            $this->data[$className]['properties'][$targetName]['JoinFromMiddle'] = $annotation;
        }
        else if($annotation instanceof JoinToMiddle)
        {
            $this->data[$className]['properties'][$targetName]['JoinToMiddle'] = $annotation;
        }
        else if($annotation instanceof AutoSelect)
        {
            $this->data[$className]['properties'][$targetName]['AutoSelect'] = $annotation;
        }
        else if($annotation instanceof AutoDelete)
        {
            $this->data[$className]['properties'][$targetName]['AutoDelete'] = $annotation;
        }
        else if($annotation instanceof AutoInsert)
        {
            $this->data[$className]['properties'][$targetName]['AutoInsert'] = $annotation;
        }
        else if($annotation instanceof AutoSave)
        {
            $this->data[$className]['properties'][$targetName]['AutoSave'] = $annotation;
        }
        else if($annotation instanceof AutoUpdate)
        {
            $this->data[$className]['properties'][$targetName]['AutoUpdate'] = $annotation;
        }
    }

    /**
     * 获取关联关系
     * ['propertyName'=>annotation]
     *
     * @param string $className
     * @return array
     */
    public function getRelations($className)
    {
        return $this->data[$className]['relations'] ?? [];
    }

    /**
     * 是否有关联关系
     * $propertyName 为null时则查询模型类是否有关联关系
     *
     * @param string $className
     * @param string $propertyName
     * @return boolean
     */
    public function hasRelation($className, $propertyName = null)
    {
        if(null === $propertyName)
        {
            return isset($this->data[$className]['relations']);
        }
        else
        {
            return isset($this->data[$className]['relations'][$propertyName]);
        }
    }

    /**
     * 获取属性注解
     *
     * @param string $className
     * @param string $propertyName
     * @param string $annotationName
     * @return \Imi\Bean\Annotation\Base|null
     */
    public function getPropertyAnnotation($className, $propertyName, $annotationName)
    {
        return $this->data[$className]['properties'][$propertyName][$annotationName] ?? null;
    }

}