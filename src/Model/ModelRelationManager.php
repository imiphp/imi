<?php

namespace Imi\Model;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\BeanFactory;
use Imi\Model\Annotation\Relation\ManyToMany;
use Imi\Model\Annotation\Relation\PolymorphicManyToMany;
use Imi\Model\Annotation\Relation\RelationBase;
use Imi\Model\Relation\Delete;
use Imi\Model\Relation\Insert;
use Imi\Model\Relation\Query;
use Imi\Model\Relation\Update;

abstract class ModelRelationManager
{
    /**
     * 模型关联字段名数组.
     *
     * @var array
     */
    private static $relationFieldsNames = [];

    /**
     * 初始化模型.
     *
     * @param \Imi\Model\Model|string $model
     *
     * @return void
     */
    public static function initModel($model)
    {
        foreach (AnnotationManager::getPropertiesAnnotations(BeanFactory::getObjectClass($model), RelationBase::class) as $propertyName => $annotations)
        {
            if (null !== $model[$propertyName])
            {
                continue;
            }
            Query::init($model, $propertyName, $annotations);
        }
    }

    /**
     * 模型是否有关联定义.
     *
     * @param \Imi\Model\Model|string $model
     *
     * @return bool
     */
    public static function hasRelation($model)
    {
        return (bool) AnnotationManager::getPropertiesAnnotations(BeanFactory::getObjectClass($model), RelationBase::class);
    }

    /**
     * 查询模型指定关联.
     *
     * @param \Imi\Model\Model|string $model
     * @param string                  ...$names
     *
     * @return void
     */
    public static function queryModelRelations($model, ...$names)
    {
        $relations = AnnotationManager::getPropertiesAnnotations(BeanFactory::getObjectClass($model), RelationBase::class);
        foreach ($names as $name)
        {
            if (isset($relations[$name]))
            {
                Query::init($model, $name, $relations[$name], true);
            }
        }
    }

    /**
     * 插入模型.
     *
     * @param \Imi\Model\Model|string $model
     *
     * @return void
     */
    public static function insertModel($model)
    {
        foreach (AnnotationManager::getPropertiesAnnotations(BeanFactory::getObjectClass($model), RelationBase::class) as $propertyName => $annotations)
        {
            if (null === $model[$propertyName])
            {
                continue;
            }
            Insert::parse($model, $propertyName, $annotations);
        }
    }

    /**
     * 更新模型.
     *
     * @param \Imi\Model\Model|string $model
     *
     * @return void
     */
    public static function updateModel($model)
    {
        foreach (AnnotationManager::getPropertiesAnnotations(BeanFactory::getObjectClass($model), RelationBase::class) as $propertyName => $annotations)
        {
            if (null === $model[$propertyName])
            {
                continue;
            }
            Update::parse($model, $propertyName, $annotations);
        }
    }

    /**
     * 删除模型.
     *
     * @param \Imi\Model\Model|string $model
     *
     * @return void
     */
    public static function deleteModel($model)
    {
        foreach (AnnotationManager::getPropertiesAnnotations(BeanFactory::getObjectClass($model), RelationBase::class) as $propertyName => $annotations)
        {
            if (null === $model[$propertyName])
            {
                continue;
            }
            Delete::parse($model, $propertyName, $annotations);
        }
    }

    /**
     * 获取当前模型关联字段名数组.
     *
     * @param string|object $object
     *
     * @return string[]
     */
    public static function getRelationFieldNames($object)
    {
        $class = BeanFactory::getObjectClass($object);
        $staticRelationFieldsNames = &static::$relationFieldsNames;
        if (isset($staticRelationFieldsNames[$class]))
        {
            return $staticRelationFieldsNames[$class];
        }
        else
        {
            $relations = AnnotationManager::getPropertiesAnnotations($class, RelationBase::class);
            $result = array_keys($relations);
            foreach ($relations as $annotations)
            {
                $annotation = $annotations[0];
                if (($annotation instanceof ManyToMany || $annotation instanceof PolymorphicManyToMany) && $annotation->rightMany)
                {
                    $result[] = $annotation->rightMany;
                }
            }

            return $staticRelationFieldsNames[$class] = $result;
        }
    }
}
