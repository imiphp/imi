<?php

declare(strict_types=1);

namespace Imi\Model\Relation;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\BeanFactory;
use Imi\Event\Event;
use Imi\Model\Annotation\Relation\AutoSave;
use Imi\Model\Annotation\Relation\AutoUpdate;
use Imi\Model\Annotation\Relation\RelationBase;
use Imi\Model\Contract\IModelQuery;
use Imi\Model\Model;
use Imi\Model\Relation\Event\ModelRelationOperationEvent;
use Imi\Model\Relation\Struct\ManyToMany;
use Imi\Model\Relation\Struct\OneToMany;
use Imi\Model\Relation\Struct\OneToOne;
use Imi\Model\Relation\Struct\PolymorphicManyToMany;
use Imi\Model\Relation\Struct\PolymorphicOneToMany;
use Imi\Model\Relation\Struct\PolymorphicOneToOne;

class Update
{
    use \Imi\Util\Traits\TStaticClass;
    use TRelation;

    private static array $methodCacheMap = [];

    /**
     * 处理更新.
     *
     * @param \Imi\Bean\Annotation\Base[] $annotations
     */
    public static function parse(Model $model, string $propertyName, array $annotations): void
    {
        if (!$model[$propertyName])
        {
            return;
        }
        $className = BeanFactory::getObjectClass($model);
        $propertyAnnotations = AnnotationManager::getPropertyAnnotations($className, $propertyName, [
            AutoUpdate::class,
            AutoSave::class,
        ], true, true);
        /** @var AutoUpdate|null $autoUpdate */
        $autoUpdate = $propertyAnnotations[AutoUpdate::class];
        /** @var AutoSave|null $autoSave */
        $autoSave = $propertyAnnotations[AutoSave::class];

        if ($autoUpdate)
        {
            if (!$autoUpdate->status)
            {
                return;
            }
        }
        elseif (!$autoSave || !$autoSave->status)
        {
            return;
        }

        $firstAnnotation = reset($annotations);

        // @phpstan-ignore-next-line
        if ($firstAnnotation instanceof \Imi\Model\Annotation\Relation\PolymorphicToOne)
        {
            // @phpstan-ignore-next-line
            static::parseByPolymorphicToOne($model, $propertyName, $annotations);
        }
        // @phpstan-ignore-next-line
        elseif ($firstAnnotation instanceof \Imi\Model\Annotation\Relation\PolymorphicOneToOne)
        {
            // @phpstan-ignore-next-line
            static::parseByPolymorphicOneToOne($model, $propertyName, $firstAnnotation);
        }
        // @phpstan-ignore-next-line
        elseif ($firstAnnotation instanceof \Imi\Model\Annotation\Relation\PolymorphicOneToMany)
        {
            // @phpstan-ignore-next-line
            static::parseByPolymorphicOneToMany($model, $propertyName, $firstAnnotation);
        }
        // @phpstan-ignore-next-line
        elseif ($firstAnnotation instanceof \Imi\Model\Annotation\Relation\PolymorphicManyToMany)
        {
            // @phpstan-ignore-next-line
            static::parseByPolymorphicManyToMany($model, $propertyName, $firstAnnotation);
        }
        elseif ($firstAnnotation instanceof \Imi\Model\Annotation\Relation\OneToOne)
        {
            static::parseByOneToOne($model, $propertyName, $firstAnnotation);
        }
        elseif ($firstAnnotation instanceof \Imi\Model\Annotation\Relation\OneToMany)
        {
            static::parseByOneToMany($model, $propertyName, $firstAnnotation);
        }
        elseif ($firstAnnotation instanceof \Imi\Model\Annotation\Relation\ManyToMany)
        {
            static::parseByManyToMany($model, $propertyName, $firstAnnotation);
        }
        elseif ($firstAnnotation instanceof \Imi\Model\Annotation\Relation\Relation)
        {
            static::parseByRelation($model, $propertyName, $firstAnnotation);
        }
    }

    /**
     * 处理一对一更新.
     */
    public static function parseByOneToOne(Model $model, string $propertyName, \Imi\Model\Annotation\Relation\OneToOne $annotation): void
    {
        $className = BeanFactory::getObjectClass($model);

        $struct = new OneToOne($className, $propertyName, $annotation);
        $leftField = $struct->getLeftField();
        $rightField = $struct->getRightField();
        $eventName = 'imi.model.relation.update.' . $className . '.' . $propertyName;

        Event::dispatch(new ModelRelationOperationEvent($eventName . '.BEFORE', $model, $propertyName, $annotation, $struct));

        $modelField = $model[$propertyName];
        $modelField[$rightField] = $model[$leftField];
        $modelField->update();
        Event::dispatch(new ModelRelationOperationEvent($eventName . '.AFTER', $model, $propertyName, $annotation, $struct));
    }

    /**
     * 处理一对多更新.
     */
    public static function parseByOneToMany(Model $model, string $propertyName, \Imi\Model\Annotation\Relation\OneToMany $annotation): void
    {
        $className = BeanFactory::getObjectClass($model);

        $struct = new OneToMany($className, $propertyName, $annotation);
        $leftField = $struct->getLeftField();
        $rightField = $struct->getRightField();
        $rightModel = $struct->getRightModel();

        $propertyAnnotations = AnnotationManager::getPropertyAnnotations($className, $propertyName, [
            AutoUpdate::class,
            AutoSave::class,
        ], true, true);
        /** @var AutoUpdate|null $autoUpdate */
        $autoUpdate = $propertyAnnotations[AutoUpdate::class];
        /** @var AutoSave|null $autoSave */
        $autoSave = $propertyAnnotations[AutoSave::class];
        // 是否删除无关数据
        if ($autoUpdate)
        {
            $orphanRemoval = $autoUpdate->orphanRemoval;
        }
        elseif ($autoSave)
        {
            $orphanRemoval = $autoSave->orphanRemoval;
        }
        else
        {
            $orphanRemoval = false;
        }

        $eventName = 'imi.model.relation.update.' . $className . '.' . $propertyName;

        Event::dispatch(new ModelRelationOperationEvent($eventName . '.BEFORE', $model, $propertyName, $annotation, $struct));
        $modelLeftValue = $model[$leftField];
        if ($orphanRemoval)
        {
            // 删除无关联数据
            $pks = $rightModel::PRIMARY_KEYS ?? $rightModel::__getMeta()->getId();
            if (isset($pks[1]))
            {
                throw new \RuntimeException(sprintf('%s can not OneToMany, because has more than 1 primary keys', $rightModel));
            }
            $pk = $pks[0];

            $oldIds = $rightModel::query(self::parsePoolName($annotation->poolName, $className, $rightModel))->where($rightField, '=', $modelLeftValue)->field($pk)->select()->getColumn();

            $updateIds = [];
            foreach ($model[$propertyName] as $row)
            {
                if (null !== $row[$pk])
                {
                    $updateIds[] = $row[$pk];
                }
                $row[$rightField] = $modelLeftValue;
            }

            $deleteIds = array_diff($oldIds, $updateIds);

            if ($deleteIds)
            {
                // 批量删除
                $rightModel::deleteBatch(static function (IModelQuery $query) use ($pk, $deleteIds): void {
                    $query->whereIn($pk, $deleteIds);
                });
            }

            foreach ($model[$propertyName] as $row)
            {
                $row->save();
            }
        }
        else
        {
            // 直接更新
            foreach ($model[$propertyName] as $row)
            {
                $row[$rightField] = $modelLeftValue;
                $row->save();
            }
        }
        Event::dispatch(new ModelRelationOperationEvent($eventName . '.AFTER', $model, $propertyName, $annotation, $struct));
    }

    /**
     * 处理多对多更新.
     */
    public static function parseByManyToMany(Model $model, string $propertyName, \Imi\Model\Annotation\Relation\ManyToMany $annotation): void
    {
        $className = BeanFactory::getObjectClass($model);

        $struct = new ManyToMany($className, $propertyName, $annotation);
        $middleModel = $struct->getMiddleModel();
        $middleLeftField = $struct->getMiddleLeftField();
        $middleRightField = $struct->getMiddleRightField();
        $leftField = $struct->getLeftField();

        $propertyAnnotations = AnnotationManager::getPropertyAnnotations($className, $propertyName, [
            AutoUpdate::class,
            AutoSave::class,
        ], true, true);
        /** @var AutoUpdate|null $autoUpdate */
        $autoUpdate = $propertyAnnotations[AutoUpdate::class];
        /** @var AutoSave|null $autoSave */
        $autoSave = $propertyAnnotations[AutoSave::class];
        // 是否删除无关数据
        if ($autoUpdate)
        {
            $orphanRemoval = $autoUpdate->orphanRemoval;
        }
        elseif ($autoSave)
        {
            $orphanRemoval = $autoSave->orphanRemoval;
        }
        else
        {
            $orphanRemoval = false;
        }

        $eventName = 'imi.model.relation.update.' . $className . '.' . $propertyName;

        Event::dispatch(new ModelRelationOperationEvent($eventName . '.BEFORE', $model, $propertyName, $annotation, $struct));
        $modelLeftValue = $model[$leftField];
        if ($orphanRemoval)
        {
            // 删除无关联数据
            $oldRightIds = $middleModel::query(self::parsePoolName($annotation->poolName, $className, $middleModel))->where($middleLeftField, '=', $modelLeftValue)->field($middleRightField)->select()->getColumn();

            $updateIds = [];
            foreach ($model[$propertyName] as $row)
            {
                if (null !== $row[$middleRightField])
                {
                    $updateIds[] = $row[$middleRightField];
                }
                $row[$middleLeftField] = $modelLeftValue;
            }

            $deleteIds = array_diff($oldRightIds, $updateIds);

            if ($deleteIds)
            {
                // 批量删除
                $middleModel::deleteBatch(static function (IModelQuery $query) use ($middleLeftField, $middleRightField, $deleteIds, $modelLeftValue): void {
                    $query->where($middleLeftField, '=', $modelLeftValue)->whereIn($middleRightField, $deleteIds);
                });
            }

            foreach ($model[$propertyName] as $row)
            {
                $row->save();
            }
        }
        else
        {
            // 直接更新
            foreach ($model[$propertyName] as $row)
            {
                $row[$middleLeftField] = $modelLeftValue;
                $row->save();
            }
        }
        Event::dispatch(new ModelRelationOperationEvent($eventName . '.AFTER', $model, $propertyName, $annotation, $struct));
    }

    /**
     * 模型类（可指定字段）是否包含更新关联关系.
     */
    public static function hasUpdateRelation(string $className, ?string $propertyName = null): bool
    {
        $relations = AnnotationManager::getPropertiesAnnotations($className, RelationBase::class);

        if (!$relations)
        {
            return false;
        }

        if (null === $propertyName)
        {
            foreach ($relations as $name => $_)
            {
                $propertyAnnotations = AnnotationManager::getPropertyAnnotations($className, $name, [
                    AutoUpdate::class,
                    AutoSave::class,
                ], true, true);
                /** @var AutoUpdate|null $autoUpdate */
                $autoUpdate = $propertyAnnotations[AutoUpdate::class];
                /** @var AutoSave|null $autoSave */
                $autoSave = $propertyAnnotations[AutoSave::class];

                if ($autoUpdate)
                {
                    if (!$autoUpdate->status)
                    {
                        continue;
                    }
                }
                elseif (!$autoSave || !$autoSave->status)
                {
                    continue;
                }
            }
        }
        else
        {
            $propertyAnnotations = AnnotationManager::getPropertyAnnotations($className, $propertyName, [
                AutoUpdate::class,
                AutoSave::class,
            ], true, true);
            /** @var AutoUpdate|null $autoUpdate */
            $autoUpdate = $propertyAnnotations[AutoUpdate::class];
            /** @var AutoSave|null $autoSave */
            $autoSave = $propertyAnnotations[AutoSave::class];

            if ($autoUpdate)
            {
                if (!$autoUpdate->status)
                {
                    return false;
                }
            }
            elseif (!$autoSave || !$autoSave->status)
            {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * 处理多态一对一更新.
     *
     * @param \Imi\Model\Annotation\Relation\PolymorphicToOne[] $annotations
     */
    public static function parseByPolymorphicToOne(Model $model, string $propertyName, array $annotations): void
    {
        foreach ($annotations as $annotationItem)
        {
            if ($model[$annotationItem->type] == $annotationItem->typeValue)
            {
                $className = BeanFactory::getObjectClass($model);
                $eventName = 'imi.model.relation.update.' . $className . '.' . $propertyName;

                Event::dispatch(new ModelRelationOperationEvent($eventName . '.BEFORE', $model, $propertyName, $annotationItem));

                $model[$propertyName]->update();

                Event::dispatch(new ModelRelationOperationEvent($eventName . '.AFTER', $model, $propertyName, $annotationItem));
                break;
            }
        }
    }

    /**
     * 处理多态一对一更新.
     */
    public static function parseByPolymorphicOneToOne(Model $model, string $propertyName, \Imi\Model\Annotation\Relation\PolymorphicOneToOne $annotation): void
    {
        $className = BeanFactory::getObjectClass($model);

        $struct = new PolymorphicOneToOne($className, $propertyName, $annotation);
        $leftField = $struct->getLeftField();
        $rightField = $struct->getRightField();
        $eventName = 'imi.model.relation.update.' . $className . '.' . $propertyName;

        Event::dispatch(new ModelRelationOperationEvent($eventName . '.BEFORE', $model, $propertyName, $annotation, $struct));

        $modelField = $model[$propertyName];
        $modelField[$rightField] = $model[$leftField];
        $modelField->{$annotation->type} = $annotation->typeValue;
        $modelField->update();
        Event::dispatch(new ModelRelationOperationEvent($eventName . '.AFTER', $model, $propertyName, $annotation, $struct));
    }

    /**
     * 处理多态一对多更新.
     */
    public static function parseByPolymorphicOneToMany(Model $model, string $propertyName, \Imi\Model\Annotation\Relation\PolymorphicOneToMany $annotation): void
    {
        $className = BeanFactory::getObjectClass($model);

        $struct = new PolymorphicOneToMany($className, $propertyName, $annotation);
        $leftField = $struct->getLeftField();
        $rightField = $struct->getRightField();
        $rightModel = $struct->getRightModel();

        $propertyAnnotations = AnnotationManager::getPropertyAnnotations($className, $propertyName, [
            AutoUpdate::class,
            AutoSave::class,
        ], true, true);
        /** @var AutoUpdate|null $autoUpdate */
        $autoUpdate = $propertyAnnotations[AutoUpdate::class];
        /** @var AutoSave|null $autoSave */
        $autoSave = $propertyAnnotations[AutoSave::class];
        // 是否删除无关数据
        if ($autoUpdate)
        {
            $orphanRemoval = $autoUpdate->orphanRemoval;
        }
        elseif ($autoSave)
        {
            $orphanRemoval = $autoSave->orphanRemoval;
        }
        else
        {
            $orphanRemoval = false;
        }

        $eventName = 'imi.model.relation.update.' . $className . '.' . $propertyName;

        Event::dispatch(new ModelRelationOperationEvent($eventName . '.BEFORE', $model, $propertyName, $annotation, $struct));
        $modelLeftValue = $model[$leftField];
        if ($orphanRemoval)
        {
            // 删除无关联数据
            $pks = $rightModel::PRIMARY_KEYS ?? $rightModel::__getMeta()->getId();
            if (isset($pks[1]))
            {
                throw new \RuntimeException(sprintf('%s can not OneToMany, because has more than 1 primary keys', $rightModel));
            }
            $pk = $pks[0];

            $oldIds = $rightModel::query(self::parsePoolName($annotation->poolName, $className, $rightModel))->where($annotation->type, '=', $annotation->typeValue)->where($rightField, '=', $modelLeftValue)->field($pk)->select()->getColumn();

            $updateIds = [];
            foreach ($model[$propertyName] as $row)
            {
                if (null !== $row[$pk])
                {
                    $updateIds[] = $row[$pk];
                }
                $row[$rightField] = $modelLeftValue;
                $row->{$annotation->type} = $annotation->typeValue;
            }

            $deleteIds = array_diff($oldIds, $updateIds);

            if ($deleteIds)
            {
                // 批量删除
                $rightModel::deleteBatch(static function (IModelQuery $query) use ($pk, $deleteIds): void {
                    $query->whereIn($pk, $deleteIds);
                });
            }

            foreach ($model[$propertyName] as $row)
            {
                $row->save();
            }
        }
        else
        {
            // 直接更新
            foreach ($model[$propertyName] as $row)
            {
                $row[$rightField] = $modelLeftValue;
                $row->save();
            }
        }
        Event::dispatch(new ModelRelationOperationEvent($eventName . '.AFTER', $model, $propertyName, $annotation, $struct));
    }

    /**
     * 处理多态多对多更新.
     */
    public static function parseByPolymorphicManyToMany(Model $model, string $propertyName, \Imi\Model\Annotation\Relation\PolymorphicManyToMany $annotation): void
    {
        $className = BeanFactory::getObjectClass($model);

        $struct = new PolymorphicManyToMany($className, $propertyName, $annotation);
        $middleModel = $struct->getMiddleModel();
        $middleLeftField = $struct->getMiddleLeftField();
        $middleRightField = $struct->getMiddleRightField();
        $leftField = $struct->getLeftField();

        $propertyAnnotations = AnnotationManager::getPropertyAnnotations($className, $propertyName, [
            AutoUpdate::class,
            AutoSave::class,
        ], true, true);
        /** @var AutoUpdate|null $autoUpdate */
        $autoUpdate = $propertyAnnotations[AutoUpdate::class];
        /** @var AutoSave|null $autoSave */
        $autoSave = $propertyAnnotations[AutoSave::class];
        // 是否删除无关数据
        if ($autoUpdate)
        {
            $orphanRemoval = $autoUpdate->orphanRemoval;
        }
        elseif ($autoSave)
        {
            $orphanRemoval = $autoSave->orphanRemoval;
        }
        else
        {
            $orphanRemoval = false;
        }

        $eventName = 'imi.model.relation.update.' . $className . '.' . $propertyName;

        Event::dispatch(new ModelRelationOperationEvent($eventName . '.BEFORE', $model, $propertyName, $annotation, $struct));
        $modelLeftValue = $model[$leftField];
        if ($orphanRemoval)
        {
            // 删除无关联数据
            $oldRightIds = $middleModel::query(self::parsePoolName($annotation->poolName, $className, $middleModel))->where($annotation->type, '=', $annotation->typeValue)->where($middleLeftField, '=', $modelLeftValue)->field($middleRightField)->select()->getColumn();

            $updateIds = [];
            foreach ($model[$propertyName] as $row)
            {
                if (null !== $row[$middleRightField])
                {
                    $updateIds[] = $row[$middleRightField];
                }
                $row[$middleLeftField] = $modelLeftValue;
                $row->{$annotation->type} = $annotation->typeValue;
            }

            $deleteIds = array_diff($oldRightIds, $updateIds);

            if ($deleteIds)
            {
                // 批量删除
                $middleModel::deleteBatch(static function (IModelQuery $query) use ($middleLeftField, $middleRightField, $deleteIds, $annotation, $modelLeftValue): void {
                    $query->where($annotation->type, '=', $annotation->typeValue)->where($middleLeftField, '=', $modelLeftValue)->whereIn($middleRightField, $deleteIds);
                });
            }

            foreach ($model[$propertyName] as $row)
            {
                $row->save();
            }
        }
        else
        {
            // 直接更新
            foreach ($model[$propertyName] as $row)
            {
                $row[$middleLeftField] = $modelLeftValue;
                $row->{$annotation->type} = $annotation->typeValue;
                $row->save();
            }
        }
        Event::dispatch(new ModelRelationOperationEvent($eventName . '.AFTER', $model, $propertyName, $annotation, $struct));
    }

    /**
     * 处理自定义关联.
     */
    public static function parseByRelation(Model $model, string $propertyName, \Imi\Model\Annotation\Relation\Relation $annotation): void
    {
        $className = $model->__getMeta()->getClassName();
        $method = (self::$methodCacheMap[$className][$propertyName] ??= ('__update' . ucfirst($propertyName)));
        $className::{$method}($model, $annotation);
    }
}
