<?php

declare(strict_types=1);

namespace Imi\Model\Relation;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\BeanFactory;
use Imi\Event\Event;
use Imi\Model\Annotation\Relation\AutoDelete;
use Imi\Model\Contract\IModelQuery;
use Imi\Model\Model;
use Imi\Model\Relation\Event\ModelRelationOperationEvent;
use Imi\Model\Relation\Struct\ManyToMany;
use Imi\Model\Relation\Struct\OneToMany;
use Imi\Model\Relation\Struct\OneToOne;
use Imi\Model\Relation\Struct\PolymorphicOneToMany;
use Imi\Model\Relation\Struct\PolymorphicOneToOne;
use Imi\Util\Imi;

class Delete
{
    use \Imi\Util\Traits\TStaticClass;
    use TRelation;

    private static array $methodCacheMap = [];

    /**
     * 处理删除.
     *
     * @param \Imi\Bean\Annotation\Base[] $annotations
     */
    public static function parse(Model $model, string $propertyName, array $annotations): void
    {
        $className = BeanFactory::getObjectClass($model);
        /** @var AutoDelete|null $autoDelete */
        $autoDelete = AnnotationManager::getPropertyAnnotations($className, $propertyName, AutoDelete::class, true, true);

        if (!$autoDelete || !$autoDelete->status)
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
     * 处理一对一删除.
     */
    public static function parseByOneToOne(Model $model, string $propertyName, \Imi\Model\Annotation\Relation\OneToOne $annotation): void
    {
        $className = BeanFactory::getObjectClass($model);

        $struct = new OneToOne($className, $propertyName, $annotation);
        $leftField = $struct->getLeftField();
        $rightField = $struct->getRightField();

        $modelField = $model[$propertyName];
        $eventName = 'imi.model.relation.delete.' . $className . '.' . $propertyName;
        Event::dispatch(new ModelRelationOperationEvent($eventName . '.BEFORE', $model, $propertyName, $annotation, $struct));
        if (null === $modelField)
        {
            $rightModel = $struct->getRightModel();
            $rightModel::query(self::parsePoolName($annotation->poolName, $className, $rightModel))->where($rightField, '=', $model[$leftField])->limit(1)->delete();
        }
        else
        {
            $modelField->delete();
        }
        Event::dispatch(new ModelRelationOperationEvent($eventName . '.AFTER', $model, $propertyName, $annotation, $struct));
    }

    /**
     * 处理一对多删除.
     */
    public static function parseByOneToMany(Model $model, string $propertyName, \Imi\Model\Annotation\Relation\OneToMany $annotation): void
    {
        $className = BeanFactory::getObjectClass($model);

        $struct = new OneToMany($className, $propertyName, $annotation);
        $leftField = $struct->getLeftField();
        $rightField = $struct->getRightField();
        $rightModel = $struct->getRightModel();
        $eventName = 'imi.model.relation.delete.' . $className . '.' . $propertyName;

        Event::dispatch(new ModelRelationOperationEvent($eventName . '.BEFORE', $model, $propertyName, $annotation, $struct));
        $rightModel::deleteBatch(static function (IModelQuery $query) use ($model, $leftField, $rightField): void {
            $query->where($rightField, '=', $model[$leftField]);
        });
        Event::dispatch(new ModelRelationOperationEvent($eventName . '.AFTER', $model, $propertyName, $annotation, $struct));
    }

    /**
     * 处理多对多删除.
     */
    public static function parseByManyToMany(Model $model, string $propertyName, \Imi\Model\Annotation\Relation\ManyToMany $annotation): void
    {
        $className = BeanFactory::getObjectClass($model);

        $struct = new ManyToMany($className, $propertyName, $annotation);
        $middleModel = $struct->getMiddleModel();
        $middleLeftField = $struct->getMiddleLeftField();
        $leftField = $struct->getLeftField();
        $eventName = 'imi.model.relation.delete.' . $className . '.' . $propertyName;

        Event::dispatch(new ModelRelationOperationEvent($eventName . '.BEFORE', $model, $propertyName, $annotation, $struct));
        $middleModel::deleteBatch(static function (IModelQuery $query) use ($model, $leftField, $middleLeftField): void {
            $query->where($middleLeftField, '=', $model[$leftField]);
        });
        Event::dispatch(new ModelRelationOperationEvent($eventName . '.AFTER', $model, $propertyName, $annotation, $struct));
    }

    /**
     * 处理多态一对一删除.
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
                $eventName = 'imi.model.relation.delete.' . $className . '.' . $propertyName;

                Event::dispatch(new ModelRelationOperationEvent($eventName . '.BEFORE', $model, $propertyName, $annotationItem));

                if (null === $model[$propertyName])
                {
                    if (class_exists($annotationItem->model))
                    {
                        $modelClass = $annotationItem->model;
                    }
                    else
                    {
                        $modelClass = Imi::getClassNamespace($className) . '\\' . $annotationItem->model;
                    }
                    $modelClass::query(self::parsePoolName($annotationItem->poolName, $className, $modelClass))->where($annotationItem->modelField, '=', $model[$annotationItem->field])->limit(1)->delete();
                }
                else
                {
                    $model[$propertyName]->delete();
                }

                Event::dispatch(new ModelRelationOperationEvent($eventName . '.AFTER', $model, $propertyName, $annotationItem));
                break;
            }
        }
    }

    /**
     * 处理多态一对一删除.
     */
    public static function parseByPolymorphicOneToOne(Model $model, string $propertyName, \Imi\Model\Annotation\Relation\PolymorphicOneToOne $annotation): void
    {
        $className = BeanFactory::getObjectClass($model);

        $struct = new PolymorphicOneToOne($className, $propertyName, $annotation);
        $leftField = $struct->getLeftField();
        $rightField = $struct->getRightField();
        $eventName = 'imi.model.relation.delete.' . $className . '.' . $propertyName;

        Event::dispatch(new ModelRelationOperationEvent($eventName . '.BEFORE', $model, $propertyName, $annotation, $struct));

        $modelField = $model[$propertyName];
        if (null === $modelField)
        {
            $rightModel = $struct->getRightModel();
            $rightModel::query(self::parsePoolName($annotation->poolName, $className, $rightModel))->where($rightField, '=', $model[$leftField])->delete();
        }
        else
        {
            $modelField[$rightField] = $model[$leftField];
            $modelField->{$annotation->type} = $annotation->typeValue;
            $modelField->delete();
        }
        Event::dispatch(new ModelRelationOperationEvent($eventName . '.AFTER', $model, $propertyName, $annotation, $struct));
    }

    /**
     * 处理多态一对多删除.
     */
    public static function parseByPolymorphicOneToMany(Model $model, string $propertyName, \Imi\Model\Annotation\Relation\PolymorphicOneToMany $annotation): void
    {
        $className = BeanFactory::getObjectClass($model);

        $struct = new PolymorphicOneToMany($className, $propertyName, $annotation);
        $leftField = $struct->getLeftField();
        $rightField = $struct->getRightField();
        $rightModel = $struct->getRightModel();
        $eventName = 'imi.model.relation.delete.' . $className . '.' . $propertyName;

        Event::dispatch(new ModelRelationOperationEvent($eventName . '.BEFORE', $model, $propertyName, $annotation, $struct));

        $rightModel::deleteBatch(static function (IModelQuery $query) use ($model, $leftField, $rightField, $annotation): void {
            $query->where($annotation->type, '=', $annotation->typeValue)->where($rightField, '=', $model[$leftField]);
        });
        Event::dispatch(new ModelRelationOperationEvent($eventName . '.AFTER', $model, $propertyName, $annotation, $struct));
    }

    /**
     * 处理多态多对多删除.
     */
    public static function parseByPolymorphicManyToMany(Model $model, string $propertyName, \Imi\Model\Annotation\Relation\PolymorphicManyToMany $annotation): void
    {
        $className = BeanFactory::getObjectClass($model);

        $struct = new ManyToMany($className, $propertyName, $annotation);
        $middleModel = $struct->getMiddleModel();
        $middleLeftField = $struct->getMiddleLeftField();
        $leftField = $struct->getLeftField();
        $eventName = 'imi.model.relation.delete.' . $className . '.' . $propertyName;

        Event::dispatch(new ModelRelationOperationEvent($eventName . '.BEFORE', $model, $propertyName, $annotation, $struct));

        $middleModel::deleteBatch(static function (IModelQuery $query) use ($model, $leftField, $middleLeftField, $annotation): void {
            $query->where($annotation->type, '=', $annotation->typeValue)->where($middleLeftField, '=', $model[$leftField]);
        });
        Event::dispatch(new ModelRelationOperationEvent($eventName . '.AFTER', $model, $propertyName, $annotation, $struct));
    }

    /**
     * 处理自定义关联.
     */
    public static function parseByRelation(Model $model, string $propertyName, \Imi\Model\Annotation\Relation\Relation $annotation): void
    {
        $className = $model->__getMeta()->getClassName();
        $method = (self::$methodCacheMap[$className][$propertyName] ??= ('__delete' . ucfirst($propertyName)));
        $className::{$method}($model, $annotation);
    }
}
