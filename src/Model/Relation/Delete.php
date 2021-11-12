<?php

declare(strict_types=1);

namespace Imi\Model\Relation;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\BeanFactory;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Event\Event;
use Imi\Model\Annotation\Relation\AutoDelete;
use Imi\Model\Model;
use Imi\Model\Relation\Struct\ManyToMany;
use Imi\Model\Relation\Struct\OneToMany;
use Imi\Model\Relation\Struct\OneToOne;
use Imi\Model\Relation\Struct\PolymorphicOneToMany;
use Imi\Model\Relation\Struct\PolymorphicOneToOne;

class Delete
{
    private function __construct()
    {
    }

    /**
     * 处理删除.
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
        /** @var AutoDelete|null $autoDelete */
        $autoDelete = AnnotationManager::getPropertyAnnotations($className, $propertyName, AutoDelete::class)[0] ?? null;

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
        $modelField[$rightField] = $model[$leftField];
        $eventName = 'IMI.MODEL.RELATION.DELETE.' . $className . '.' . $propertyName;
        Event::trigger($eventName . '.BEFORE', [
            'model'        => $model,
            'propertyName' => $propertyName,
            'annotation'   => $annotation,
            'struct'       => $struct,
        ]);
        $modelField->delete();
        Event::trigger($eventName . '.AFTER', [
            'model'        => $model,
            'propertyName' => $propertyName,
            'annotation'   => $annotation,
            'struct'       => $struct,
        ]);
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
        $eventName = 'IMI.MODEL.RELATION.DELETE.' . $className . '.' . $propertyName;

        Event::trigger($eventName . '.BEFORE', [
            'model'        => $model,
            'propertyName' => $propertyName,
            'annotation'   => $annotation,
            'struct'       => $struct,
        ]);
        $rightModel::deleteBatch(function (IQuery $query) use ($model, $leftField, $rightField) {
            $query->where($rightField, '=', $model[$leftField]);
        });
        Event::trigger($eventName . '.AFTER', [
            'model'        => $model,
            'propertyName' => $propertyName,
            'annotation'   => $annotation,
            'struct'       => $struct,
        ]);
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
        $eventName = 'IMI.MODEL.RELATION.DELETE.' . $className . '.' . $propertyName;

        Event::trigger($eventName . '.BEFORE', [
            'model'        => $model,
            'propertyName' => $propertyName,
            'annotation'   => $annotation,
            'struct'       => $struct,
        ]);
        $middleModel::deleteBatch(function (IQuery $query) use ($model, $leftField, $middleLeftField) {
            $query->where($middleLeftField, '=', $model[$leftField]);
        });
        Event::trigger($eventName . '.AFTER', [
            'model'        => $model,
            'propertyName' => $propertyName,
            'annotation'   => $annotation,
            'struct'       => $struct,
        ]);
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
            if ($model->{$annotationItem->type} == $annotationItem->typeValue)
            {
                $className = BeanFactory::getObjectClass($model);
                $eventName = 'IMI.MODEL.RELATION.DELETE.' . $className . '.' . $propertyName;

                Event::trigger($eventName . '.BEFORE', [
                    'model'        => $model,
                    'propertyName' => $propertyName,
                    'annotation'   => $annotationItem,
                ]);

                $model[$propertyName]->delete();

                Event::trigger($eventName . '.AFTER', [
                    'model'        => $model,
                    'propertyName' => $propertyName,
                    'annotation'   => $annotationItem,
                ]);
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
        $eventName = 'IMI.MODEL.RELATION.DELETE.' . $className . '.' . $propertyName;

        Event::trigger($eventName . '.BEFORE', [
            'model'        => $model,
            'propertyName' => $propertyName,
            'annotation'   => $annotation,
            'struct'       => $struct,
        ]);

        $modelField = $model[$propertyName];
        $modelField[$rightField] = $model[$leftField];
        $modelField->{$annotation->type} = $annotation->typeValue;
        $modelField->delete();
        Event::trigger($eventName . '.AFTER', [
            'model'        => $model,
            'propertyName' => $propertyName,
            'annotation'   => $annotation,
            'struct'       => $struct,
        ]);
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
        $eventName = 'IMI.MODEL.RELATION.DELETE.' . $className . '.' . $propertyName;

        Event::trigger($eventName . '.BEFORE', [
            'model'        => $model,
            'propertyName' => $propertyName,
            'annotation'   => $annotation,
            'struct'       => $struct,
        ]);

        $rightModel::deleteBatch(function (IQuery $query) use ($model, $leftField, $rightField, $annotation) {
            $query->where($annotation->type, '=', $annotation->typeValue)->where($rightField, '=', $model[$leftField]);
        });
        Event::trigger($eventName . '.AFTER', [
            'model'        => $model,
            'propertyName' => $propertyName,
            'annotation'   => $annotation,
            'struct'       => $struct,
        ]);
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
        $eventName = 'IMI.MODEL.RELATION.DELETE.' . $className . '.' . $propertyName;

        Event::trigger($eventName . '.BEFORE', [
            'model'        => $model,
            'propertyName' => $propertyName,
            'annotation'   => $annotation,
            'struct'       => $struct,
        ]);

        $middleModel::deleteBatch(function (IQuery $query) use ($model, $leftField, $middleLeftField, $annotation) {
            $query->where($annotation->type, '=', $annotation->typeValue)->where($middleLeftField, '=', $model[$leftField]);
        });
        Event::trigger($eventName . '.AFTER', [
            'model'        => $model,
            'propertyName' => $propertyName,
            'annotation'   => $annotation,
            'struct'       => $struct,
        ]);
    }
}
