<?php

declare(strict_types=1);

namespace Imi\Model\Relation;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\BeanFactory;
use Imi\Event\Event;
use Imi\Model\Annotation\Relation\AutoInsert;
use Imi\Model\Annotation\Relation\AutoSave;
use Imi\Model\Model;
use Imi\Model\Relation\Event\ModelRelationOperationEvent;
use Imi\Model\Relation\Struct\ManyToMany;
use Imi\Model\Relation\Struct\OneToMany;
use Imi\Model\Relation\Struct\OneToOne;
use Imi\Model\Relation\Struct\PolymorphicManyToMany;
use Imi\Model\Relation\Struct\PolymorphicOneToMany;
use Imi\Model\Relation\Struct\PolymorphicOneToOne;

class Insert
{
    use \Imi\Util\Traits\TStaticClass;
    use TRelation;

    private static array $methodCacheMap = [];

    /**
     * 处理插入.
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
            AutoInsert::class,
            AutoSave::class,
        ], true, true);
        /** @var AutoInsert|null $autoInsert */
        $autoInsert = $propertyAnnotations[AutoInsert::class];
        /** @var AutoSave|null $autoSave */
        $autoSave = $propertyAnnotations[AutoSave::class];

        if ($autoInsert)
        {
            if (!$autoInsert->status)
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
     * 处理一对一插入.
     */
    public static function parseByOneToOne(Model $model, string $propertyName, \Imi\Model\Annotation\Relation\OneToOne $annotation): void
    {
        $className = BeanFactory::getObjectClass($model);

        $struct = new OneToOne($className, $propertyName, $annotation);
        $leftField = $struct->getLeftField();
        $rightField = $struct->getRightField();
        $eventName = 'imi.model.relation.insert.' . $className . '.' . $propertyName;

        Event::dispatch(new ModelRelationOperationEvent($eventName . '.BEFORE', $model, $propertyName, $annotation, $struct));

        $modelField = $model[$propertyName];
        $modelField[$rightField] = $model[$leftField];
        $modelField->insert();
        Event::dispatch(new ModelRelationOperationEvent($eventName . '.AFTER', $model, $propertyName, $annotation, $struct));
    }

    /**
     * 处理一对多插入.
     */
    public static function parseByOneToMany(Model $model, string $propertyName, \Imi\Model\Annotation\Relation\OneToMany $annotation): void
    {
        $className = BeanFactory::getObjectClass($model);

        $struct = new OneToMany($className, $propertyName, $annotation);
        $leftField = $struct->getLeftField();
        $rightField = $struct->getRightField();
        $rightModel = $struct->getRightModel();
        $eventName = 'imi.model.relation.insert.' . $className . '.' . $propertyName;

        Event::dispatch(new ModelRelationOperationEvent($eventName . '.BEFORE', $model, $propertyName, $annotation, $struct));

        foreach ($model[$propertyName] as $index => $row)
        {
            if (!$row instanceof $rightModel)
            {
                $row = $rightModel::newInstance($row);
                $model[$propertyName][$index] = $row;
            }
            /** @var \Imi\Model\Model $row */
            $row[$rightField] = $model[$leftField];
            $row->insert();
        }
        Event::dispatch(new ModelRelationOperationEvent($eventName . '.AFTER', $model, $propertyName, $annotation, $struct));
    }

    /**
     * 处理多对多插入.
     */
    public static function parseByManyToMany(Model $model, string $propertyName, \Imi\Model\Annotation\Relation\ManyToMany $annotation): void
    {
        $className = BeanFactory::getObjectClass($model);

        $struct = new ManyToMany($className, $propertyName, $annotation);
        $middleModel = $struct->getMiddleModel();
        $middleLeftField = $struct->getMiddleLeftField();
        $leftField = $struct->getLeftField();
        $eventName = 'imi.model.relation.insert.' . $className . '.' . $propertyName;

        Event::dispatch(new ModelRelationOperationEvent($eventName . '.BEFORE', $model, $propertyName, $annotation, $struct));

        foreach ($model[$propertyName] as $index => $row)
        {
            if (!$row instanceof $middleModel)
            {
                $row = $middleModel::newInstance($row);
                $model[$propertyName][$index] = $row;
            }
            /** @var \Imi\Model\Model $row */
            $row[$middleLeftField] = $model[$leftField];
            $row->insert();
        }
        Event::dispatch(new ModelRelationOperationEvent($eventName . '.AFTER', $model, $propertyName, $annotation, $struct));
    }

    /**
     * 处理多态一对一插入.
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
                $eventName = 'imi.model.relation.insert.' . $className . '.' . $propertyName;

                Event::dispatch(new ModelRelationOperationEvent($eventName . '.BEFORE', $model, $propertyName, $annotationItem));

                $modelField = $annotationItem->modelField;
                $field = $annotationItem->field;

                $rightModel = $model[$propertyName];
                $rightModel[$modelField] = $model[$field];
                $rightModel->insert();

                Event::dispatch(new ModelRelationOperationEvent($eventName . '.AFTER', $model, $propertyName, $annotationItem));
                break;
            }
        }
    }

    /**
     * 处理多态一对一插入.
     */
    public static function parseByPolymorphicOneToOne(Model $model, string $propertyName, \Imi\Model\Annotation\Relation\PolymorphicOneToOne $annotation): void
    {
        $className = BeanFactory::getObjectClass($model);

        $struct = new PolymorphicOneToOne($className, $propertyName, $annotation);
        $leftField = $struct->getLeftField();
        $rightField = $struct->getRightField();
        $eventName = 'imi.model.relation.insert.' . $className . '.' . $propertyName;

        Event::dispatch(new ModelRelationOperationEvent($eventName . '.BEFORE', $model, $propertyName, $annotation, $struct));

        $modelField = $model[$propertyName];
        $modelField[$rightField] = $model[$leftField];
        $modelField->{$annotation->type} = $annotation->typeValue;
        $modelField->insert();
        Event::dispatch(new ModelRelationOperationEvent($eventName . '.AFTER', $model, $propertyName, $annotation, $struct));
    }

    /**
     * 处理多态一对多插入.
     */
    public static function parseByPolymorphicOneToMany(Model $model, string $propertyName, \Imi\Model\Annotation\Relation\PolymorphicOneToMany $annotation): void
    {
        $className = BeanFactory::getObjectClass($model);

        $struct = new PolymorphicOneToMany($className, $propertyName, $annotation);
        $leftField = $struct->getLeftField();
        $rightField = $struct->getRightField();
        $rightModel = $struct->getRightModel();
        $eventName = 'imi.model.relation.insert.' . $className . '.' . $propertyName;

        Event::dispatch(new ModelRelationOperationEvent($eventName . '.BEFORE', $model, $propertyName, $annotation, $struct));

        foreach ($model[$propertyName] as $index => $row)
        {
            if (!$row instanceof $rightModel)
            {
                $row = $rightModel::newInstance($row);
                $model[$propertyName][$index] = $row;
            }
            /** @var \Imi\Model\Model $row */
            $row[$rightField] = $model[$leftField];
            $row[$annotation->type] = $annotation->typeValue;
            $row->insert();
        }
        Event::dispatch(new ModelRelationOperationEvent($eventName . '.AFTER', $model, $propertyName, $annotation, $struct));
    }

    /**
     * 处理多态多对多插入.
     */
    public static function parseByPolymorphicManyToMany(Model $model, string $propertyName, \Imi\Model\Annotation\Relation\PolymorphicManyToMany $annotation): void
    {
        $className = BeanFactory::getObjectClass($model);

        $struct = new PolymorphicManyToMany($className, $propertyName, $annotation);
        $middleModel = $struct->getMiddleModel();
        $middleLeftField = $struct->getMiddleLeftField();
        $leftField = $struct->getLeftField();
        $eventName = 'imi.model.relation.insert.' . $className . '.' . $propertyName;

        Event::dispatch(new ModelRelationOperationEvent($eventName . '.BEFORE', $model, $propertyName, $annotation, $struct));

        foreach ($model[$propertyName] as $index => $row)
        {
            if (!$row instanceof $middleModel)
            {
                $row = $middleModel::newInstance($row);
                $model[$propertyName][$index] = $row;
            }
            /** @var \Imi\Model\Model $row */
            $row[$middleLeftField] = $model[$leftField];
            $row[$annotation->type] = $annotation->typeValue;
            $row->insert();
        }
        Event::dispatch(new ModelRelationOperationEvent($eventName . '.AFTER', $model, $propertyName, $annotation, $struct));
    }

    /**
     * 处理自定义关联.
     */
    public static function parseByRelation(Model $model, string $propertyName, \Imi\Model\Annotation\Relation\Relation $annotation): void
    {
        $className = $model->__getMeta()->getClassName();
        $method = (self::$methodCacheMap[$className][$propertyName] ??= ('__insert' . ucfirst($propertyName)));
        $className::{$method}($model, $annotation);
    }
}
