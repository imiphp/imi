<?php

declare(strict_types=1);

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

class ModelRelationManager
{
    /**
     * 模型关联字段名数组.
     */
    private static array $relationFieldsNames = [];

    private function __construct()
    {
    }

    /**
     * 初始化模型.
     *
     * @param \Imi\Model\Model|string $model
     */
    public static function initModel($model): void
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
     */
    public static function hasRelation($model): bool
    {
        return (bool) AnnotationManager::getPropertiesAnnotations(BeanFactory::getObjectClass($model), RelationBase::class);
    }

    /**
     * 查询模型指定关联.
     *
     * @param \Imi\Model\Model|string $model
     * @param string                  ...$names
     */
    public static function queryModelRelations($model, string ...$names): void
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
     */
    public static function insertModel($model): void
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
     */
    public static function updateModel($model): void
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
     */
    public static function deleteModel($model): void
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
    public static function getRelationFieldNames($object): array
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
                // @phpstan-ignore-next-line
                if (($annotation instanceof ManyToMany || $annotation instanceof PolymorphicManyToMany) && $annotation->rightMany)
                {
                    $result[] = $annotation->rightMany;
                }
            }

            return $staticRelationFieldsNames[$class] = $result;
        }
    }
}
