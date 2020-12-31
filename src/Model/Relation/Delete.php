<?php

declare(strict_types=1);

namespace Imi\Model\Relation;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\BeanFactory;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Event\Event;
use Imi\Model\Annotation\Relation\AutoDelete;
use Imi\Model\Annotation\Relation\RelationBase;
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
     * @param \Imi\Model\Model                            $model
     * @param string                                      $propertyName
     * @param \Imi\Model\Annotation\Relation\RelationBase $annotation
     *
     * @return void
     */
    public static function parse(Model $model, string $propertyName, RelationBase $annotation)
    {
        if (!$model->$propertyName)
        {
            return;
        }
        $className = BeanFactory::getObjectClass($model);
        $autoDelete = AnnotationManager::getPropertyAnnotations($className, $propertyName, AutoDelete::class)[0] ?? null;

        if (!$autoDelete || !$autoDelete->status)
        {
            return;
        }
        if ($annotation instanceof \Imi\Model\Annotation\Relation\OneToOne)
        {
            static::parseByOneToOne($model, $propertyName, $annotation);
        }
        elseif ($annotation instanceof \Imi\Model\Annotation\Relation\OneToMany)
        {
            static::parseByOneToMany($model, $propertyName, $annotation);
        }
        elseif ($annotation instanceof \Imi\Model\Annotation\Relation\ManyToMany)
        {
            static::parseByManyToMany($model, $propertyName, $annotation);
        }
        elseif ($annotation instanceof \Imi\Model\Annotation\Relation\PolymorphicOneToOne)
        {
            static::parseByPolymorphicOneToOne($model, $propertyName, $annotation);
        }
        elseif ($annotation instanceof \Imi\Model\Annotation\Relation\PolymorphicOneToMany)
        {
            static::parseByPolymorphicOneToMany($model, $propertyName, $annotation);
        }
        elseif ($annotation instanceof \Imi\Model\Annotation\Relation\PolymorphicManyToMany)
        {
            static::parseByPolymorphicManyToMany($model, $propertyName, $annotation);
        }
    }

    /**
     * 处理一对一删除.
     *
     * @param \Imi\Model\Model                        $model
     * @param string                                  $propertyName
     * @param \Imi\Model\Annotation\Relation\OneToOne $annotation
     *
     * @return void
     */
    public static function parseByOneToOne(Model $model, string $propertyName, \Imi\Model\Annotation\Relation\OneToOne $annotation)
    {
        $className = BeanFactory::getObjectClass($model);

        $struct = new OneToOne($className, $propertyName, $annotation);
        $leftField = $struct->getLeftField();
        $rightField = $struct->getRightField();

        $modelField = $model->$propertyName;
        $modelField->$rightField = $model->$leftField;
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
     *
     * @param \Imi\Model\Model                         $model
     * @param string                                   $propertyName
     * @param \Imi\Model\Annotation\Relation\OneToMany $annotation
     *
     * @return void
     */
    public static function parseByOneToMany(Model $model, string $propertyName, \Imi\Model\Annotation\Relation\OneToMany $annotation)
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
            $query->where($rightField, '=', $model->$leftField);
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
     *
     * @param \Imi\Model\Model                          $model
     * @param string                                    $propertyName
     * @param \Imi\Model\Annotation\Relation\ManyToMany $annotation
     *
     * @return void
     */
    public static function parseByManyToMany(Model $model, string $propertyName, \Imi\Model\Annotation\Relation\ManyToMany $annotation)
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
            $query->where($middleLeftField, '=', $model->$leftField);
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
     * @param \Imi\Model\Model                                   $model
     * @param string                                             $propertyName
     * @param \Imi\Model\Annotation\Relation\PolymorphicOneToOne $annotation
     *
     * @return void
     */
    public static function parseByPolymorphicOneToOne(Model $model, string $propertyName, \Imi\Model\Annotation\Relation\PolymorphicOneToOne $annotation)
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

        $modelField = $model->$propertyName;
        $modelField->$rightField = $model->$leftField;
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
     *
     * @param \Imi\Model\Model                                    $model
     * @param string                                              $propertyName
     * @param \Imi\Model\Annotation\Relation\PolymorphicOneToMany $annotation
     *
     * @return void
     */
    public static function parseByPolymorphicOneToMany(Model $model, string $propertyName, \Imi\Model\Annotation\Relation\PolymorphicOneToMany $annotation)
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
            $query->where($annotation->type, '=', $annotation->typeValue)->where($rightField, '=', $model->$leftField);
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
     *
     * @param \Imi\Model\Model                                     $model
     * @param string                                               $propertyName
     * @param \Imi\Model\Annotation\Relation\PolymorphicManyToMany $annotation
     *
     * @return void
     */
    public static function parseByPolymorphicManyToMany(Model $model, string $propertyName, \Imi\Model\Annotation\Relation\PolymorphicManyToMany $annotation)
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
            $query->where($annotation->type, '=', $annotation->typeValue)->where($middleLeftField, '=', $model->$leftField);
        });
        Event::trigger($eventName . '.AFTER', [
            'model'        => $model,
            'propertyName' => $propertyName,
            'annotation'   => $annotation,
            'struct'       => $struct,
        ]);
    }
}
