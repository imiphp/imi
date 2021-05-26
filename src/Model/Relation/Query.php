<?php

namespace Imi\Model\Relation;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\BeanFactory;
use Imi\Db\Db;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Event\Event;
use Imi\Model\Annotation\Relation\AutoSelect;
use Imi\Model\Annotation\Relation\RelationBase;
use Imi\Model\Relation\Struct\ManyToMany;
use Imi\Model\Relation\Struct\OneToMany;
use Imi\Model\Relation\Struct\OneToOne;
use Imi\Model\Relation\Struct\PolymorphicManyToMany;
use Imi\Model\Relation\Struct\PolymorphicOneToMany;
use Imi\Model\Relation\Struct\PolymorphicOneToOne;
use Imi\Util\ArrayList;
use Imi\Util\ClassObject;
use Imi\Util\Imi;

abstract class Query
{
    /**
     * 初始化.
     *
     * @param \Imi\Model\Model                                      $model
     * @param string                                                $propertyName
     * @param \Imi\Bean\Annotation\Base|\Imi\Bean\Annotation\Base[] $annotation
     * @param bool                                                  $forceInit    是否强制更新
     *
     * @return void
     */
    public static function init($model, $propertyName, $annotation, $forceInit = false)
    {
        $className = BeanFactory::getObjectClass($model);

        if (!$forceInit)
        {
            /** @var AutoSelect|null $autoSelect */
            $autoSelect = AnnotationManager::getPropertyAnnotations($className, $propertyName, AutoSelect::class)[0] ?? null;
            if ($autoSelect && !$autoSelect->status)
            {
                return;
            }
        }

        if (\is_array($annotation))
        {
            $firstAnnotation = reset($annotation);
            // @phpstan-ignore-next-line
            if ($firstAnnotation instanceof \Imi\Model\Annotation\Relation\PolymorphicToOne)
            {
                static::initByPolymorphicToOne($model, $propertyName, $annotation);
            }
        }
        elseif ($annotation instanceof \Imi\Model\Annotation\Relation\OneToOne)
        {
            static::initByOneToOne($model, $propertyName, $annotation);
        }
        elseif ($annotation instanceof \Imi\Model\Annotation\Relation\OneToMany)
        {
            static::initByOneToMany($model, $propertyName, $annotation);
        }
        elseif ($annotation instanceof \Imi\Model\Annotation\Relation\ManyToMany)
        {
            static::initByManyToMany($model, $propertyName, $annotation);
        }
        elseif ($annotation instanceof \Imi\Model\Annotation\Relation\PolymorphicOneToOne)
        {
            static::initByPolymorphicOneToOne($model, $propertyName, $annotation);
        }
        elseif ($annotation instanceof \Imi\Model\Annotation\Relation\PolymorphicOneToMany)
        {
            static::initByPolymorphicOneToMany($model, $propertyName, $annotation);
        }
        elseif ($annotation instanceof \Imi\Model\Annotation\Relation\PolymorphicManyToMany)
        {
            static::initByPolymorphicManyToMany($model, $propertyName, $annotation);
        }
        elseif ($annotation instanceof \Imi\Model\Annotation\Relation\PolymorphicToMany)
        {
            static::initByPolymorphicToMany($model, $propertyName, $annotation);
        }
    }

    /**
     * 初始化一对一关系.
     *
     * @param \Imi\Model\Model                        $model
     * @param string                                  $propertyName
     * @param \Imi\Model\Annotation\Relation\OneToOne $annotation
     *
     * @return void
     */
    public static function initByOneToOne($model, $propertyName, $annotation)
    {
        $className = BeanFactory::getObjectClass($model);

        if (class_exists($annotation->model))
        {
            $modelClass = $annotation->model;
        }
        else
        {
            $modelClass = Imi::getClassNamespace($className) . '\\' . $annotation->model;
        }

        $struct = new OneToOne($className, $propertyName, $annotation);
        $leftField = $struct->getLeftField();
        $rightField = $struct->getRightField();
        $eventName = 'IMI.MODEL.RELATION.QUERY.' . $className . '.' . $propertyName;

        if (null === $model->$leftField)
        {
            $rightModel = $modelClass::newInstance();
        }
        else
        {
            /** @var IQuery $query */
            $query = $modelClass::query()->where($rightField, '=', $model->$leftField);
            if ($annotation->fields)
            {
                $query->field(...$annotation->fields);
            }
            Event::trigger($eventName . '.BEFORE', [
                'model'        => $model,
                'propertyName' => $propertyName,
                'annotation'   => $annotation,
                'struct'       => $struct,
                'query'        => $query,
            ]);
            $rightModel = $query->select()->get();
            if (null === $rightModel)
            {
                $rightModel = $modelClass::newInstance();
            }
        }

        $model->$propertyName = $rightModel;
        Event::trigger($eventName . '.AFTER', [
            'model'        => $model,
            'propertyName' => $propertyName,
            'annotation'   => $annotation,
            'struct'       => $struct,
        ]);
    }

    /**
     * 初始化一对多关系.
     *
     * @param \Imi\Model\Model                         $model
     * @param string                                   $propertyName
     * @param \Imi\Model\Annotation\Relation\OneToMany $annotation
     *
     * @return void
     */
    public static function initByOneToMany($model, $propertyName, $annotation)
    {
        $className = BeanFactory::getObjectClass($model);

        if (class_exists($annotation->model))
        {
            $modelClass = $annotation->model;
        }
        else
        {
            $modelClass = Imi::getClassNamespace($className) . '\\' . $annotation->model;
        }

        $struct = new OneToMany($className, $propertyName, $annotation);
        $leftField = $struct->getLeftField();
        $rightField = $struct->getRightField();
        $eventName = 'IMI.MODEL.RELATION.QUERY.' . $className . '.' . $propertyName;

        $model->$propertyName = new ArrayList($modelClass);
        if (null !== $model->$leftField)
        {
            /** @var IQuery $query */
            $query = $modelClass::query()->where($rightField, '=', $model->$leftField);
            if ($annotation->fields)
            {
                $query->field(...$annotation->fields);
            }
            if ($annotation->order)
            {
                $query->orderRaw($annotation->order);
            }
            Event::trigger($eventName . '.BEFORE', [
                'model'        => $model,
                'propertyName' => $propertyName,
                'annotation'   => $annotation,
                'struct'       => $struct,
                'query'        => $query,
            ]);
            $list = $query->select()->getArray();
            if (null !== $list)
            {
                $model->$propertyName->append(...$list);
            }
        }
        Event::trigger($eventName . '.AFTER', [
            'model'        => $model,
            'propertyName' => $propertyName,
            'annotation'   => $annotation,
            'struct'       => $struct,
        ]);
    }

    /**
     * 初始化多对多关系.
     *
     * @param \Imi\Model\Model                          $model
     * @param string                                    $propertyName
     * @param \Imi\Model\Annotation\Relation\ManyToMany $annotation
     *
     * @return void
     */
    public static function initByManyToMany($model, $propertyName, $annotation)
    {
        $className = BeanFactory::getObjectClass($model);

        $struct = new ManyToMany($className, $propertyName, $annotation);
        $leftField = $struct->getLeftField();
        $rightField = $struct->getRightField();
        $middleTable = $struct->getMiddleModel()::__getMeta()->getTableName();
        $rightTable = $struct->getRightModel()::__getMeta()->getTableName();

        static::parseManyToManyQueryFields($struct->getMiddleModel(), $struct->getRightModel(), $middleFields, $rightFields);
        $fields = static::mergeManyToManyFields($middleTable, $middleFields, $rightTable, $rightFields);

        $model->$propertyName = new ArrayList($struct->getMiddleModel());
        $model->{$annotation->rightMany} = new ArrayList($struct->getRightModel());
        $eventName = 'IMI.MODEL.RELATION.QUERY.' . $className . '.' . $propertyName;

        if (null !== $model->$leftField)
        {
            $query = Db::query($className::__getMeta()->getDbPoolName())
                        ->table($rightTable)
                        ->field(...$fields)
                        ->join($middleTable, $middleTable . '.' . $struct->getMiddleRightField(), '=', $rightTable . '.' . $rightField)
                        ->where($middleTable . '.' . $struct->getMiddleLeftField(), '=', $model->$leftField);
            if ($annotation->order)
            {
                $query->orderRaw($annotation->order);
            }
            Event::trigger($eventName . '.BEFORE', [
                'model'        => $model,
                'propertyName' => $propertyName,
                'annotation'   => $annotation,
                'struct'       => $struct,
                'query'        => $query,
            ]);
            $list = $query->select()
                          ->getArray();
            if (null !== $list)
            {
                // 关联数据
                static::appendMany($model->$propertyName, $list, $middleFields, $struct->getMiddleModel());

                // 右侧表数据
                static::appendMany($model->{$annotation->rightMany}, $list, $rightFields, $struct->getRightModel());
            }
        }
        Event::trigger($eventName . '.AFTER', [
            'model'        => $model,
            'propertyName' => $propertyName,
            'annotation'   => $annotation,
            'struct'       => $struct,
        ]);
    }

    /**
     * 初始化多态一对一关系.
     *
     * @param \Imi\Model\Model                                   $model
     * @param string                                             $propertyName
     * @param \Imi\Model\Annotation\Relation\PolymorphicOneToOne $annotation
     *
     * @return void
     */
    public static function initByPolymorphicOneToOne($model, $propertyName, $annotation)
    {
        $className = BeanFactory::getObjectClass($model);

        if (class_exists($annotation->model))
        {
            $modelClass = $annotation->model;
        }
        else
        {
            $modelClass = Imi::getClassNamespace($className) . '\\' . $annotation->model;
        }

        $struct = new PolymorphicOneToOne($className, $propertyName, $annotation);
        $leftField = $struct->getLeftField();
        $rightField = $struct->getRightField();
        $eventName = 'IMI.MODEL.RELATION.QUERY.' . $className . '.' . $propertyName;

        if (null === $model->$leftField)
        {
            $rightModel = $modelClass::newInstance();
        }
        else
        {
            /** @var IQuery $query */
            $query = $modelClass::query()->where($annotation->type, '=', $annotation->typeValue)->where($rightField, '=', $model->$leftField);
            if ($annotation->fields)
            {
                $query->field(...$annotation->fields);
            }
            Event::trigger($eventName . '.BEFORE', [
                'model'        => $model,
                'propertyName' => $propertyName,
                'annotation'   => $annotation,
                'struct'       => $struct,
                'query'        => $query,
            ]);
            $rightModel = $query->select()->get();
            if (null === $rightModel)
            {
                $rightModel = $modelClass::newInstance();
            }
        }

        $model->$propertyName = $rightModel;
        Event::trigger($eventName . '.AFTER', [
            'model'        => $model,
            'propertyName' => $propertyName,
            'annotation'   => $annotation,
            'struct'       => $struct,
        ]);
    }

    /**
     * 初始化多态一对多关系.
     *
     * @param \Imi\Model\Model                                    $model
     * @param string                                              $propertyName
     * @param \Imi\Model\Annotation\Relation\PolymorphicOneToMany $annotation
     *
     * @return void
     */
    public static function initByPolymorphicOneToMany($model, $propertyName, $annotation)
    {
        $className = BeanFactory::getObjectClass($model);

        if (class_exists($annotation->model))
        {
            $modelClass = $annotation->model;
        }
        else
        {
            $modelClass = Imi::getClassNamespace($className) . '\\' . $annotation->model;
        }

        $struct = new PolymorphicOneToMany($className, $propertyName, $annotation);
        $leftField = $struct->getLeftField();
        $rightField = $struct->getRightField();
        $eventName = 'IMI.MODEL.RELATION.QUERY.' . $className . '.' . $propertyName;

        $model->$propertyName = $modelPropery = new ArrayList($modelClass);
        if (null !== $model->$leftField)
        {
            /** @var IQuery $query */
            $query = $modelClass::query()->where($annotation->type, '=', $annotation->typeValue)->where($rightField, '=', $model->$leftField);
            if ($annotation->fields)
            {
                $query->field(...$annotation->fields);
            }
            if ($annotation->order)
            {
                $query->orderRaw($annotation->order);
            }
            Event::trigger($eventName . '.BEFORE', [
                'model'        => $model,
                'propertyName' => $propertyName,
                'annotation'   => $annotation,
                'struct'       => $struct,
                'query'        => $query,
            ]);
            $list = $query->select()->getArray();
            if (null !== $list)
            {
                $modelPropery->append(...$list);
            }
        }
        Event::trigger($eventName . '.AFTER', [
            'model'        => $model,
            'propertyName' => $propertyName,
            'annotation'   => $annotation,
            'struct'       => $struct,
        ]);
    }

    /**
     * 初始化多态，对应的实体模型.
     *
     * @param \Imi\Model\Model                                  $model
     * @param string                                            $propertyName
     * @param \Imi\Model\Annotation\Relation\PolymorphicToOne[] $annotation
     *
     * @return void
     */
    public static function initByPolymorphicToOne($model, $propertyName, $annotation)
    {
        $className = BeanFactory::getObjectClass($model);
        $eventName = 'IMI.MODEL.RELATION.QUERY.' . $className . '.' . $propertyName;
        foreach ($annotation as $annotationItem)
        {
            if ($model->{$annotationItem->type} == $annotationItem->typeValue)
            {
                $leftField = $annotationItem->modelField;
                $rightField = $annotationItem->field;
                if (class_exists($annotationItem->model))
                {
                    $modelClass = $annotationItem->model;
                }
                else
                {
                    $modelClass = $className . '\\' . $annotationItem->model;
                }
                if (null === $model->$rightField)
                {
                    $leftModel = $modelClass::newInstance();
                }
                else
                {
                    /** @var IQuery $query */
                    $query = $modelClass::query()->where($leftField, '=', $model->$rightField);
                    if ($annotationItem->fields)
                    {
                        $query->field(...$annotationItem->fields);
                    }
                    Event::trigger($eventName . '.BEFORE', [
                        'model'        => $model,
                        'propertyName' => $propertyName,
                        'annotation'   => $annotation,
                        'query'        => $query,
                    ]);
                    $leftModel = $query->select()->get();
                    if (null === $leftModel)
                    {
                        $leftModel = $modelClass::newInstance();
                    }
                }
                $model->$propertyName = $leftModel;
                break;
            }
        }
        Event::trigger($eventName . '.AFTER', [
            'model'        => $model,
            'propertyName' => $propertyName,
            'annotation'   => $annotation,
        ]);
    }

    /**
     * 初始化多态，对应的实体模型列表.
     *
     * @param \Imi\Model\Model                                 $model
     * @param string                                           $propertyName
     * @param \Imi\Model\Annotation\Relation\PolymorphicToMany $annotation
     *
     * @return void
     */
    public static function initByPolymorphicToMany($model, $propertyName, $annotation)
    {
        $className = BeanFactory::getObjectClass($model);

        $struct = new PolymorphicManyToMany($className, $propertyName, $annotation);
        $leftField = $struct->getLeftField();
        $rightField = $struct->getRightField();
        $middleTable = $struct->getMiddleModel()::__getMeta()->getTableName();
        $rightTable = $struct->getRightModel()::__getMeta()->getTableName();

        static::parseManyToManyQueryFields($struct->getMiddleModel(), $struct->getRightModel(), $middleFields, $rightFields);
        $fields = static::mergeManyToManyFields($middleTable, $middleFields, $rightTable, $rightFields);

        $model->$propertyName = new ArrayList($struct->getRightModel());
        $eventName = 'IMI.MODEL.RELATION.QUERY.' . $className . '.' . $propertyName;

        if (null !== $model->$leftField)
        {
            $query = Db::query($className::__getMeta()->getDbPoolName())
                        ->table($rightTable)
                        ->field(...$fields)
                        ->join($middleTable, $middleTable . '.' . $struct->getMiddleLeftField(), '=', $rightTable . '.' . $rightField)
                        ->where($middleTable . '.' . $annotation->type, '=', $annotation->typeValue)
                        ->where($middleTable . '.' . $struct->getMiddleRightField(), '=', $model->$leftField);
            if ($annotation->order)
            {
                $query->orderRaw($annotation->order);
            }
            Event::trigger($eventName . '.BEFORE', [
                'model'        => $model,
                'propertyName' => $propertyName,
                'annotation'   => $annotation,
                'struct'       => $struct,
                'query'        => $query,
            ]);
            $list = $query->select()
                          ->getArray();
            if (null !== $list)
            {
                // 关联数据
                static::appendMany($model->$propertyName, $list, $rightFields, $struct->getRightModel());
            }
        }
        Event::trigger($eventName . '.AFTER', [
            'model'        => $model,
            'propertyName' => $propertyName,
            'annotation'   => $annotation,
            'struct'       => $struct,
        ]);
    }

    /**
     * 初始化多态多对多关系.
     *
     * @param \Imi\Model\Model                                     $model
     * @param string                                               $propertyName
     * @param \Imi\Model\Annotation\Relation\PolymorphicManyToMany $annotation
     *
     * @return void
     */
    public static function initByPolymorphicManyToMany($model, $propertyName, $annotation)
    {
        $className = BeanFactory::getObjectClass($model);

        $struct = new PolymorphicManyToMany($className, $propertyName, $annotation);
        $leftField = $struct->getLeftField();
        $rightField = $struct->getRightField();
        $middleTable = $struct->getMiddleModel()::__getMeta()->getTableName();
        $rightTable = $struct->getRightModel()::__getMeta()->getTableName();

        static::parseManyToManyQueryFields($struct->getMiddleModel(), $struct->getRightModel(), $middleFields, $rightFields);
        $fields = static::mergeManyToManyFields($middleTable, $middleFields, $rightTable, $rightFields);

        $model->$propertyName = new ArrayList($struct->getMiddleModel());
        $model->{$annotation->rightMany} = new ArrayList($struct->getRightModel());
        $eventName = 'IMI.MODEL.RELATION.QUERY.' . $className . '.' . $propertyName;

        if (null !== $model->$leftField)
        {
            $query = Db::query($className::__getMeta()->getDbPoolName())
                        ->table($rightTable)
                        ->field(...$fields)
                        ->join($middleTable, $middleTable . '.' . $struct->getMiddleRightField(), '=', $rightTable . '.' . $rightField)
                        ->where($middleTable . '.' . $annotation->type, '=', $annotation->typeValue)
                        ->where($middleTable . '.' . $struct->getMiddleLeftField(), '=', $model->$leftField);
            Event::trigger($eventName . '.BEFORE', [
                'model'        => $model,
                'propertyName' => $propertyName,
                'annotation'   => $annotation,
                'struct'       => $struct,
                'query'        => $query,
            ]);
            $list = $query->select()
                          ->getArray();
            if (null !== $list)
            {
                // 关联数据
                static::appendMany($model->$propertyName, $list, $middleFields, $struct->getMiddleModel());

                // 右侧表数据
                static::appendMany($model->{$annotation->rightMany}, $list, $rightFields, $struct->getRightModel());
            }
        }
        Event::trigger($eventName . '.AFTER', [
            'model'        => $model,
            'propertyName' => $propertyName,
            'annotation'   => $annotation,
            'struct'       => $struct,
        ]);
    }

    /**
     * 初始化关联属性.
     *
     * @param \Imi\Model\Model $model
     * @param string           $propertyName
     *
     * @return void
     */
    public static function initRelations($model, $propertyName)
    {
        $className = BeanFactory::getObjectClass($model);
        $annotation = AnnotationManager::getPropertyAnnotations($className, $propertyName, RelationBase::class)[0] ?? null;
        if (null !== $annotation)
        {
            if ($annotation instanceof \Imi\Model\Annotation\Relation\OneToOne)
            {
                $model->$propertyName = (ClassObject::parseSameLevelClassName($annotation->model, $className) . '::newInstance')();
            }
            elseif ($annotation instanceof \Imi\Model\Annotation\Relation\OneToMany)
            {
                $model->$propertyName = new ArrayList(ClassObject::parseSameLevelClassName($annotation->model, $className));
            }
            elseif ($annotation instanceof \Imi\Model\Annotation\Relation\ManyToMany)
            {
                $model->$propertyName = new ArrayList(ClassObject::parseSameLevelClassName($annotation->middle, $className));
            }
            elseif ($annotation instanceof \Imi\Model\Annotation\Relation\PolymorphicOneToOne)
            {
                $model->$propertyName = (ClassObject::parseSameLevelClassName($annotation->model, $className) . '::newInstance')();
            }
            elseif ($annotation instanceof \Imi\Model\Annotation\Relation\PolymorphicOneToMany)
            {
                $model->$propertyName = new ArrayList(ClassObject::parseSameLevelClassName($annotation->model, $className));
            }
            elseif ($annotation instanceof \Imi\Model\Annotation\Relation\PolymorphicManyToMany)
            {
                $model->$propertyName = new ArrayList(ClassObject::parseSameLevelClassName($annotation->middle, $className));
            }
            else
            {
                return;
            }
        }
    }

    /**
     * 处理多对多查询用的字段，需要是"表名.字段名"，防止冲突
     *
     * @param string $middleModel
     * @param string $rightModel
     * @param array  $middleFields
     * @param array  $rightFields
     *
     * @return void
     */
    private static function parseManyToManyQueryFields($middleModel, $rightModel, &$middleFields, &$rightFields)
    {
        $middleFields = [];
        $rightFields = [];

        $middleTable = $middleModel::__getMeta()->getTableName();
        $rightTable = $rightModel::__getMeta()->getTableName();

        foreach ($middleModel::__getMeta()->getDbFields() as $name => $_)
        {
            $middleFields[$middleTable . '_' . $name] = $name;
        }

        foreach ($rightModel::__getMeta()->getDbFields() as $name => $_)
        {
            $rightFields[$rightTable . '_' . $name] = $name;
        }
    }

    /**
     * 合并多对多查询字段.
     *
     * @param string $middleTable
     * @param array  $middleFields
     * @param string $rightTable
     * @param array  $rightFields
     *
     * @return array
     */
    private static function mergeManyToManyFields($middleTable, $middleFields, $rightTable, $rightFields)
    {
        $result = [];
        foreach ($middleFields as $alias => $fieldName)
        {
            $result[] = $middleTable . '.' . $fieldName . ' ' . $alias;
        }
        foreach ($rightFields as $alias => $fieldName)
        {
            $result[] = $rightTable . '.' . $fieldName . ' ' . $alias;
        }

        return $result;
    }

    /**
     * 追加到Many列表.
     *
     * @param \Imi\Util\ArrayList $manyList
     * @param array               $dataList
     * @param array               $fields
     * @param string              $modelClass
     *
     * @return void
     */
    private static function appendMany($manyList, $dataList, $fields, $modelClass)
    {
        foreach ($dataList as $row)
        {
            $tmpRow = [];
            foreach ($fields as $alias => $fieldName)
            {
                $tmpRow[$fieldName] = $row[$alias];
            }
            $manyList->append($modelClass::createFromRecord($tmpRow));
        }
    }
}
