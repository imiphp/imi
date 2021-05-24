<?php

declare(strict_types=1);

namespace Imi\Model\Relation;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\BeanFactory;
use Imi\Db\Db;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Event\Event;
use Imi\Model\Annotation\Relation\AutoSelect;
use Imi\Model\Annotation\Relation\RelationBase;
use Imi\Model\Model;
use Imi\Model\Relation\Struct\ManyToMany;
use Imi\Model\Relation\Struct\OneToMany;
use Imi\Model\Relation\Struct\OneToOne;
use Imi\Model\Relation\Struct\PolymorphicManyToMany;
use Imi\Model\Relation\Struct\PolymorphicOneToMany;
use Imi\Model\Relation\Struct\PolymorphicOneToOne;
use Imi\Util\ArrayList;
use Imi\Util\ClassObject;
use Imi\Util\Imi;

class Query
{
    private function __construct()
    {
    }

    /**
     * 初始化.
     *
     * @param \Imi\Bean\Annotation\Base|\Imi\Bean\Annotation\Base[] $annotation
     * @param bool                                                  $forceInit  是否强制更新
     */
    public static function init(Model $model, string $propertyName, $annotation, bool $forceInit = false): void
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
        // @phpstan-ignore-next-line
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
     */
    public static function initByOneToOne(Model $model, string $propertyName, \Imi\Model\Annotation\Relation\OneToOne $annotation): void
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
     */
    public static function initByOneToMany(Model $model, string $propertyName, \Imi\Model\Annotation\Relation\OneToMany $annotation): void
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
     */
    public static function initByManyToMany(Model $model, string $propertyName, \Imi\Model\Annotation\Relation\ManyToMany $annotation): void
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
     */
    public static function initByPolymorphicOneToOne(Model $model, string $propertyName, \Imi\Model\Annotation\Relation\PolymorphicOneToOne $annotation): void
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
     */
    public static function initByPolymorphicOneToMany(Model $model, string $propertyName, \Imi\Model\Annotation\Relation\PolymorphicOneToMany $annotation): void
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
     * @param \Imi\Model\Annotation\Relation\PolymorphicToOne[] $annotation
     */
    public static function initByPolymorphicToOne(Model $model, string $propertyName, array $annotation): void
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
     */
    public static function initByPolymorphicToMany(Model $model, string $propertyName, \Imi\Model\Annotation\Relation\PolymorphicToMany $annotation): void
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
     */
    public static function initByPolymorphicManyToMany(Model $model, string $propertyName, \Imi\Model\Annotation\Relation\PolymorphicManyToMany $annotation): void
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
     */
    public static function initRelations(Model $model, string $propertyName): void
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
            // @phpstan-ignore-next-line
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
     */
    private static function parseManyToManyQueryFields(string $middleModel, string $rightModel, ?array &$middleFields, ?array &$rightFields): void
    {
        $middleFields = [];
        $rightFields = [];

        $middleTable = $middleModel::__getMeta()->getTableName();
        $rightTable = $rightModel::__getMeta()->getTableName();

        foreach ($middleModel::__getMeta()->getFieldNames() as $name)
        {
            $middleFields[$middleTable . '_' . $name] = $name;
        }

        foreach ($rightModel::__getMeta()->getFieldNames() as $name)
        {
            $rightFields[$rightTable . '_' . $name] = $name;
        }
    }

    /**
     * 合并多对多查询字段.
     */
    private static function mergeManyToManyFields(string $middleTable, array $middleFields, string $rightTable, array $rightFields): array
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
     */
    private static function appendMany(ArrayList $manyList, array $dataList, array $fields, string $modelClass): void
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
