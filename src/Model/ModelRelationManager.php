<?php

declare(strict_types=1);

namespace Imi\Model;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\BeanFactory;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Model\Annotation\Relation\ManyToMany;
use Imi\Model\Annotation\Relation\OneToMany;
use Imi\Model\Annotation\Relation\OneToOne;
use Imi\Model\Annotation\Relation\PolymorphicManyToMany;
use Imi\Model\Annotation\Relation\PolymorphicOneToMany;
use Imi\Model\Annotation\Relation\PolymorphicOneToOne;
use Imi\Model\Annotation\Relation\PolymorphicToMany;
use Imi\Model\Annotation\Relation\PolymorphicToOne;
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
     */
    public static function initModel(Model $model): void
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
     * 初始化模型列表.
     *
     * @param Model[] $models
     */
    public static function initModels(array $models, ?array $fields = null, ?string $modelClass = null): void
    {
        if (null !== $modelClass)
        {
            $modelClass = BeanFactory::getObjectClass(reset($models));
        }
        $refData = [];

        foreach (AnnotationManager::getPropertiesAnnotations($modelClass, RelationBase::class) as $propertyName => $annotations)
        {
            foreach ($models as $model)
            {
                if (null !== $model[$propertyName])
                {
                    continue;
                }
                /** @var RelationBase $firstAnnotation */
                $firstAnnotation = $annotations[0];
                if ($firstAnnotation->with || ($fields && (isset($fields[$propertyName]) || \in_array($propertyName, $fields))))
                {
                    Query::init($model, $propertyName, $annotations, true, $refData);
                }
                else
                {
                    Query::init($model, $propertyName, $annotations);
                }
            }
        }
        if ($refData)
        {
            foreach ($refData as $propertyName => $item)
            {
                $annotation = $item['annotation'];
                if ($annotation instanceof PolymorphicManyToMany)
                {
                    $rightTable = $item['rightTable'];
                    $middleTable = $item['middleTable'];
                    $middleLeftField = $item['middleLeftField'];
                    $middleRightField = $item['middleRightField'];
                    $rightField = $item['rightField'];
                    $middleModel = $item['middleModel'];
                    $rightModel = $item['rightModel'];
                    $rightMany = $annotation->rightMany;
                    $models = $item['models'];
                    $queryFields = $item['fields'];

                    $ids = $item['ids'];
                    // $rightModel可能要换，TODO
                    $query = $rightModel::query($modelClass::__getMeta()->getDbPoolName())
                                ->field(...$queryFields)
                                ->join($middleTable, $middleTable . '.' . $middleRightField, '=', $rightTable . '.' . $rightField)
                                ->where($middleTable . '.' . $annotation->type, '=', $annotation->typeValue)
                                ->where($middleTable . '.' . $middleLeftField, 'in', $ids);
                    if ($annotation->order)
                    {
                        $query->orderRaw($annotation->order);
                    }
                    if (null !== $annotation->limit)
                    {
                        $query->limit($annotation->limit);
                    }
                    if (isset($fields[$propertyName]))
                    {
                        $fields[$propertyName]($query);
                    }
                    $list = $query->select()
                                  ->getArray();
                    if ($list)
                    {
                        $appendList = [];
                        $middleLeftFieldFullName = $middleTable . '_' . $middleLeftField;
                        foreach ($list as $row)
                        {
                            $appendList[$row[$middleLeftFieldFullName]][] = $row;
                        }
                        foreach ($ids as $leftValue)
                        {
                            $tmpList = $appendList[$leftValue];
                            foreach ($models[$leftValue] as $model)
                            {
                                // 关联数据
                                Query::appendMany($model->$propertyName, $tmpList, $middleTable, $middleModel);

                                // 右侧表数据
                                $model->$rightMany->append(...$tmpList);
                            }
                        }
                    }
                }
                elseif ($annotation instanceof OneToOne)
                {
                    $rightField = $item['rightField'];
                    $query = ($item['modelClass'])::query()->whereIn($rightField, $item['ids']);
                    $models = $item['models'];
                    if ($annotation->fields)
                    {
                        $query->field(...$annotation->fields);
                    }
                    if (isset($fields[$propertyName]))
                    {
                        $fields[$propertyName]($query);
                    }
                    foreach ($query->select()->getArray() as $resultModel)
                    {
                        foreach ($models[$resultModel[$rightField]] as $model)
                        {
                            $model[$propertyName] = $resultModel;
                        }
                    }
                }
                elseif ($annotation instanceof OneToMany)
                {
                    $rightField = $item['rightField'];
                    $query = ($item['modelClass'])::query()->whereIn($rightField, $item['ids']);
                    $models = $item['models'];
                    if ($annotation->fields)
                    {
                        $query->field(...$annotation->fields);
                    }
                    if ($annotation->order)
                    {
                        $query->orderRaw($annotation->order);
                    }
                    if (null !== $annotation->limit)
                    {
                        $query->limit($annotation->limit);
                    }
                    if (isset($fields[$propertyName]))
                    {
                        $fields[$propertyName]($query);
                    }
                    foreach ($query->select()->getArray() as $resultModel)
                    {
                        foreach ($models[$resultModel[$rightField]] as $model)
                        {
                            $model[$propertyName]->append($resultModel);
                        }
                    }
                }
                elseif ($annotation instanceof ManyToMany)
                {
                    $rightTable = $item['rightTable'];
                    $middleTable = $item['middleTable'];
                    $middleLeftField = $item['middleLeftField'];
                    $middleRightField = $item['middleRightField'];
                    $rightField = $item['rightField'];
                    $middleModel = $item['middleModel'];
                    $rightModel = $item['rightModel'];
                    $rightMany = $annotation->rightMany;
                    $models = $item['models'];
                    $queryFields = $item['fields'];

                    $ids = $item['ids'];
                    $query = $rightModel::query($modelClass::__getMeta()->getDbPoolName())
                                ->field(...$queryFields)
                                ->join($middleTable, $middleTable . '.' . $middleRightField, '=', $rightTable . '.' . $rightField)
                                ->where($middleTable . '.' . $middleLeftField, 'in', $ids);
                    if ($annotation->order)
                    {
                        $query->orderRaw($annotation->order);
                    }
                    if (null !== $annotation->limit)
                    {
                        $query->limit($annotation->limit);
                    }
                    if (isset($fields[$propertyName]))
                    {
                        $fields[$propertyName]($query);
                    }
                    $list = $query->select()
                                  ->getArray();
                    if ($list)
                    {
                        $appendList = [];
                        $middleLeftFieldFullName = $middleTable . '_' . $middleLeftField;
                        foreach ($list as $row)
                        {
                            $appendList[$row[$middleLeftFieldFullName]][] = $row;
                        }
                        foreach ($ids as $leftValue)
                        {
                            $tmpList = $appendList[$leftValue];
                            foreach ($models[$leftValue] as $model)
                            {
                                // 关联数据
                                Query::appendMany($model->$propertyName, $tmpList, $middleTable, $middleModel);

                                // 右侧表数据
                                $model->$rightMany->append(...$tmpList);
                            }
                        }
                    }
                }
                elseif ($annotation instanceof PolymorphicOneToOne)
                {
                    $rightField = $item['rightField'];
                    $query = ($item['modelClass'])::query()->where($annotation->type, '=', $annotation->typeValue)->whereIn($rightField, $item['ids']);
                    $models = $item['models'];
                    if ($annotation->fields)
                    {
                        $query->field(...$annotation->fields);
                    }
                    if (isset($fields[$propertyName]))
                    {
                        $fields[$propertyName]($query);
                    }
                    foreach ($query->select()->getArray() as $resultModel)
                    {
                        foreach ($models[$resultModel[$rightField]] as $model)
                        {
                            $model[$propertyName] = $resultModel;
                        }
                    }
                }
                elseif ($annotation instanceof PolymorphicOneToMany)
                {
                    $rightField = $item['rightField'];
                    $query = $item['modelClass']::query()->where($annotation->type, '=', $annotation->typeValue)->whereIn($rightField, $item['ids']);
                    $models = $item['models'];
                    if ($annotation->fields)
                    {
                        $query->field(...$annotation->fields);
                    }
                    if ($annotation->order)
                    {
                        $query->orderRaw($annotation->order);
                    }
                    if (null !== $annotation->limit)
                    {
                        $query->limit($annotation->limit);
                    }
                    if (isset($fields[$propertyName]))
                    {
                        $fields[$propertyName]($query);
                    }
                    foreach ($query->select()->getArray() as $resultModel)
                    {
                        foreach ($models[$resultModel[$rightField]] as $model)
                        {
                            $model[$propertyName]->append($resultModel);
                        }
                    }
                }
                elseif ($annotation instanceof PolymorphicToOne)
                {
                    foreach ($item['list'] as $subItem)
                    {
                        /** @var PolymorphicToOne $subAnnotation */
                        $subAnnotation = $subItem['annotation'];
                        $leftField = $subItem['leftField'];
                        $models = $subItem['models'];
                        /** @var IQuery $query */
                        $query = $subItem['modelClass']::query()->where($leftField, 'in', $subItem['ids']);
                        if ($subAnnotation->fields)
                        {
                            $query->field(...$subAnnotation->fields);
                        }
                        if (isset($fields[$propertyName]))
                        {
                            $fields[$propertyName]($query);
                        }
                        foreach ($query->select()->getArray() as $resultModel)
                        {
                            foreach ($models[$resultModel[$leftField]] as $model)
                            {
                                $model[$propertyName] = $resultModel;
                            }
                        }
                    }
                }
                elseif ($annotation instanceof PolymorphicToMany)
                {
                    foreach ($item['list'] as $subItem)
                    {
                        $rightTable = $subItem['rightTable'];
                        $middleTable = $subItem['middleTable'];
                        $middleLeftField = $subItem['middleLeftField'];
                        $middleRightField = $subItem['middleRightField'];
                        $rightField = $subItem['rightField'];
                        $middleModel = $subItem['middleModel'];
                        $rightModel = $subItem['rightModel'];
                        $models = $subItem['models'];
                        $queryFields = $subItem['fields'];

                        $ids = $subItem['ids'];
                        $query = $rightModel::query($modelClass::__getMeta()->getDbPoolName())
                                    ->field(...$queryFields)
                                    ->join($middleTable, $middleTable . '.' . $middleLeftField, '=', $rightTable . '.' . $rightField)
                                    ->where($middleTable . '.' . $annotation->type, '=', $annotation->typeValue)
                                    ->where($middleTable . '.' . $middleRightField, 'in', $ids);
                        if ($annotation->order)
                        {
                            $query->orderRaw($annotation->order);
                        }
                        if (null !== $annotation->limit)
                        {
                            $query->limit($annotation->limit);
                        }
                        if (isset($fields[$propertyName]))
                        {
                            $fields[$propertyName]($query);
                        }
                        $list = $query->select()
                                      ->getArray();
                        if ($list)
                        {
                            $appendList = [];
                            $middleRightFieldFullName = $middleTable . '_' . $middleRightField;
                            foreach ($list as $row)
                            {
                                $appendList[$row[$middleRightFieldFullName]][] = $row;
                            }
                            foreach ($ids as $leftValue)
                            {
                                $tmpList = $appendList[$leftValue];
                                foreach ($models[$leftValue] as $model)
                                {
                                    $model->$propertyName->append(...$tmpList);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * 模型是否有关联定义.
     *
     * @param string|Model $model
     */
    public static function hasRelation($model): bool
    {
        return (bool) AnnotationManager::getPropertiesAnnotations(BeanFactory::getObjectClass($model), RelationBase::class);
    }

    /**
     * 查询模型指定关联.
     *
     * @param string ...$names
     */
    public static function queryModelRelations(Model $model, string ...$names): void
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
     */
    public static function insertModel(Model $model): void
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
     */
    public static function updateModel(Model $model): void
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
     */
    public static function deleteModel(Model $model): void
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
     * @param string|BaseModel $object
     *
     * @return string[]
     */
    public static function getRelationFieldNames($object): array
    {
        $class = BeanFactory::getObjectClass($object);
        $staticRelationFieldsNames = &self::$relationFieldsNames;
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
