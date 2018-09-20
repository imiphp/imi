<?php
namespace Imi\Model\Relation;

use Imi\Db\Db;
use Imi\Util\Imi;
use Imi\Util\Text;
use Imi\Util\ArrayList;
use Imi\Bean\BeanFactory;
use Imi\Model\ModelManager;
use Imi\Model\Parser\RelationParser;
use Imi\Model\Relation\Struct\OneToOne;
use Imi\Model\Relation\Struct\OneToMany;
use Imi\Model\Relation\Struct\ManyToMany;
use Imi\Model\Relation\Struct\PolymorphicOneToOne;
use Imi\Model\Relation\Struct\PolymorphicOneToMany;
use Imi\Model\Relation\Struct\PolymorphicManyToMany;


abstract class Query
{
    /**
     * 初始化
     *
     * @param \Imi\Model\Model $model
     * @param string $propertyName
     * @param \Imi\Bean\Annotation\Base|\Imi\Bean\Annotation\Base[] $annotation
     * @param boolean $forceInit 是否强制更新
     * @return void
     */
    public static function init($model, $propertyName, $annotation, $forceInit = false)
    {
        $relationParser = RelationParser::getInstance();
        $className = BeanFactory::getObjectClass($model);

        if(!$forceInit)
        {
            $autoSelect = $relationParser->getPropertyAnnotation($className, $propertyName, 'AutoSelect');
            if($autoSelect && !$autoSelect->status)
            {
                return;
            }
        }

        if(is_array($annotation))
        {
            $firstAnnotation = reset($annotation);
            if($firstAnnotation instanceof \Imi\Model\Annotation\Relation\PolymorphicToOne)
            {
                static::initByPolymorphicToOne($model, $propertyName, $annotation);
            }
        }
        else if($annotation instanceof \Imi\Model\Annotation\Relation\OneToOne)
        {
            static::initByOneToOne($model, $propertyName, $annotation);
        }
        else if($annotation instanceof \Imi\Model\Annotation\Relation\OneToMany)
        {
            static::initByOneToMany($model, $propertyName, $annotation);
        }
        else if($annotation instanceof \Imi\Model\Annotation\Relation\ManyToMany)
        {
            static::initByManyToMany($model, $propertyName, $annotation);
        }
        else if($annotation instanceof \Imi\Model\Annotation\Relation\PolymorphicOneToOne)
        {
            static::initByPolymorphicOneToOne($model, $propertyName, $annotation);
        }
        else if($annotation instanceof \Imi\Model\Annotation\Relation\PolymorphicOneToMany)
        {
            static::initByPolymorphicOneToMany($model, $propertyName, $annotation);
        }
        else if($annotation instanceof \Imi\Model\Annotation\Relation\PolymorphicManyToMany)
        {
            static::initByPolymorphicManyToMany($model, $propertyName, $annotation);
        }
        else if($annotation instanceof \Imi\Model\Annotation\Relation\PolymorphicToMany)
        {
            static::initByPolymorphicToMany($model, $propertyName, $annotation);
        }
    }

    /**
     * 初始化一对一关系
     *
     * @param \Imi\Model\Model $model
     * @param string $propertyName
     * @param \Imi\Model\Annotation\Relation\OneToOne $annotation
     * @return void
     */
    public static function initByOneToOne($model, $propertyName, $annotation)
    {
        $className = BeanFactory::getObjectClass($model);

        if(class_exists($annotation->model))
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

        if(null === $model->$leftField)
        {
            $rightModel = $modelClass::newInstance();
        }
        else
        {
            $rightModel = $modelClass::query()->where($rightField, '=', $model->$leftField)->select()->get();
            if(null === $rightModel)
            {
                $rightModel = $modelClass::newInstance();
            }
        }

        $model->$propertyName = $rightModel;
    }

    /**
     * 初始化一对多关系
     *
     * @param \Imi\Model\Model $model
     * @param string $propertyName
     * @param \Imi\Model\Annotation\Relation\OneToMany $annotation
     * @return void
     */
    public static function initByOneToMany($model, $propertyName, $annotation)
    {
        $className = BeanFactory::getObjectClass($model);

        if(class_exists($annotation->model))
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

        $model->$propertyName = new ArrayList($modelClass);
        if(null !== $model->$leftField)
        {
            $list = $modelClass::query()->where($rightField, '=', $model->$leftField)->select()->getArray();
            if(null !== $list)
            {
                $model->$propertyName->append(...$list);
            }
        }

    }

    /**
     * 初始化多对多关系
     *
     * @param \Imi\Model\Model $model
     * @param string $propertyName
     * @param \Imi\Model\Annotation\Relation\ManyToMany $annotation
     * @return void
     */
    public static function initByManyToMany($model, $propertyName, $annotation)
    {
        $className = BeanFactory::getObjectClass($model);

        if(class_exists($annotation->model))
        {
            $modelClass = $annotation->model;
        }
        else
        {
            $modelClass = Imi::getClassNamespace($className) . '\\' . $annotation->model;
        }

        $struct = new ManyToMany($className, $propertyName, $annotation);
        $leftField = $struct->getLeftField();
        $rightField = $struct->getRightField();
        $middleTable = ModelManager::getTable($struct->getMiddleModel());
        $rightTable = ModelManager::getTable($struct->getRightModel());

        static::parseManyToManyQueryFields($struct->getMiddleModel(), $struct->getRightModel(), $middleFields, $rightFields);
        $fields = static::mergeManyToManyFields($middleTable, $middleFields, $rightTable, $rightFields);
        
        $model->$propertyName = new ArrayList($struct->getMiddleModel());
        $model->{$annotation->rightMany} = new ArrayList($struct->getRightModel());
        
        if(null !== $model->$leftField)
        {
            $list = Db::query(ModelManager::getDbPoolName($className))
                        ->table($rightTable)
                        ->field(...$fields)
                        ->join($middleTable, $middleTable . '.' . $struct->getMiddleRightField(), '=', $rightTable . '.' . $rightField)
                        ->where($middleTable . '.' . $struct->getMiddleLeftField(), '=', $model->$leftField)
                        ->select()
                        ->getArray();
            if(null !== $list)
            {
                // 关联数据
                static::appendMany($model->$propertyName, $list, $middleFields, $struct->getMiddleModel());

                // 右侧表数据
                static::appendMany($model->{$annotation->rightMany}, $list, $rightFields, $struct->getRightModel());

            }
        }
    }

    /**
     * 初始化多态一对一关系
     *
     * @param \Imi\Model\Model $model
     * @param string $propertyName
     * @param \Imi\Model\Annotation\Relation\PolymorphicOneToOne $annotation
     * @return void
     */
    public static function initByPolymorphicOneToOne($model, $propertyName, $annotation)
    {
        $className = BeanFactory::getObjectClass($model);

        if(class_exists($annotation->model))
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

        if(null === $model->$leftField)
        {
            $rightModel = $modelClass::newInstance();
        }
        else
        {
            $rightModel = $modelClass::query()->where($annotation->type, '=', $annotation->typeValue)->where($rightField, '=', $model->$leftField)->select()->get();
            if(null === $rightModel)
            {
                $rightModel = $modelClass::newInstance();
            }
        }

        $model->$propertyName = $rightModel;
    }

    /**
     * 初始化多态一对多关系
     *
     * @param \Imi\Model\Model $model
     * @param string $propertyName
     * @param \Imi\Model\Annotation\Relation\PolymorphicOneToMany $annotation
     * @return void
     */
    public static function initByPolymorphicOneToMany($model, $propertyName, $annotation)
    {
        $className = BeanFactory::getObjectClass($model);

        if(class_exists($annotation->model))
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

        $model->$propertyName = new ArrayList($modelClass);
        if(null !== $model->$leftField)
        {
            $list = $modelClass::query()->where($annotation->type, '=', $annotation->typeValue)->where($rightField, '=', $model->$leftField)->select()->getArray();
            if(null !== $list)
            {
                $model->$propertyName->append(...$list);
            }
        }
    }

    /**
     * 初始化多态，对应的实体模型
     *
     * @param \Imi\Model\Model $model
     * @param string $propertyName
     * @param \Imi\Model\Annotation\Relation\PolymorphicToOne[] $annotation
     * @return void
     */
    public static function initByPolymorphicToOne($model, $propertyName, $annotation)
    {
        foreach($annotation as $annotationItem)
        {
            if($model->{$annotationItem->type} == $annotationItem->typeValue)
            {
                $leftField = $annotationItem->modelField;
                $rightField = $annotationItem->field;
                if(class_exists($annotationItem->model))
                {
                    $modelClass = $annotationItem->model;
                }
                else
                {
                    $modelClass = Imi::getClassNamespace($className) . '\\' . $annotationItem->model;
                }
                if(null === $model->$rightField)
                {
                    $leftModel = $modelClass::newInstance();
                }
                else
                {
                    $leftModel = $modelClass::query()->where($leftField, '=', $model->$rightField)->select()->get();
                    if(null === $leftModel)
                    {
                        $leftModel = $modelClass::newInstance();
                    }
                }
                $model->$propertyName = $leftModel;
                break;
            }
        }
    }
    
    /**
     * 初始化多态，对应的实体模型列表
     *
     * @param \Imi\Model\Model $model
     * @param string $propertyName
     * @param \Imi\Model\Annotation\Relation\PolymorphicToMany $annotation
     * @return void
     */
    public static function initByPolymorphicToMany($model, $propertyName, $annotation)
    {
        $className = BeanFactory::getObjectClass($model);

        if(class_exists($annotation->model))
        {
            $modelClass = $annotation->model;
        }
        else
        {
            $modelClass = Imi::getClassNamespace($className) . '\\' . $annotation->model;
        }

        $struct = new PolymorphicManyToMany($className, $propertyName, $annotation);
        $leftField = $struct->getLeftField();
        $rightField = $struct->getRightField();
        $middleTable = ModelManager::getTable($struct->getMiddleModel());
        $rightTable = ModelManager::getTable($struct->getRightModel());

        static::parseManyToManyQueryFields($struct->getMiddleModel(), $struct->getRightModel(), $middleFields, $rightFields);
        $fields = static::mergeManyToManyFields($middleTable, $middleFields, $rightTable, $rightFields);
        
        $model->$propertyName = new ArrayList($struct->getRightModel());
        
        if(null !== $model->$leftField)
        {
            $list = Db::query(ModelManager::getDbPoolName($className))
                        ->table($rightTable)
                        ->field(...$fields)
                        ->join($middleTable, $middleTable . '.' . $struct->getMiddleLeftField(), '=', $rightTable . '.' . $rightField)
                        ->where($middleTable . '.' . $annotation->type, '=', $annotation->typeValue)
                        ->where($middleTable . '.' . $struct->getMiddleRightField(), '=', $model->$leftField)
                        ->select()
                        ->getArray();
            if(null !== $list)
            {
                // 关联数据
                static::appendMany($model->$propertyName, $list, $rightFields, $struct->getRightModel());
            }
        }
    }

    /**
     * 初始化多态多对多关系
     *
     * @param \Imi\Model\Model $model
     * @param string $propertyName
     * @param \Imi\Model\Annotation\Relation\PolymorphicManyToMany $annotation
     * @return void
     */
    public static function initByPolymorphicManyToMany($model, $propertyName, $annotation)
    {
        $className = BeanFactory::getObjectClass($model);

        if(class_exists($annotation->model))
        {
            $modelClass = $annotation->model;
        }
        else
        {
            $modelClass = Imi::getClassNamespace($className) . '\\' . $annotation->model;
        }

        $struct = new PolymorphicManyToMany($className, $propertyName, $annotation);
        $leftField = $struct->getLeftField();
        $rightField = $struct->getRightField();
        $middleTable = ModelManager::getTable($struct->getMiddleModel());
        $rightTable = ModelManager::getTable($struct->getRightModel());

        static::parseManyToManyQueryFields($struct->getMiddleModel(), $struct->getRightModel(), $middleFields, $rightFields);
        $fields = static::mergeManyToManyFields($middleTable, $middleFields, $rightTable, $rightFields);
        
        $model->$propertyName = new ArrayList($struct->getMiddleModel());
        $model->{$annotation->rightMany} = new ArrayList($struct->getRightModel());
        
        if(null !== $model->$leftField)
        {
            $list = Db::query(ModelManager::getDbPoolName($className))
                        ->table($rightTable)
                        ->field(...$fields)
                        ->join($middleTable, $middleTable . '.' . $struct->getMiddleRightField(), '=', $rightTable . '.' . $rightField)
                        ->where($middleTable . '.' . $annotation->type, '=', $annotation->typeValue)
                        ->where($middleTable . '.' . $struct->getMiddleLeftField(), '=', $model->$leftField)
                        ->select()
                        ->getArray();
            if(null !== $list)
            {
                // 关联数据
                static::appendMany($model->$propertyName, $list, $middleFields, $struct->getMiddleModel());

                // 右侧表数据
                static::appendMany($model->{$annotation->rightMany}, $list, $rightFields, $struct->getRightModel());

            }
        }
    }

    /**
     * 处理多对多查询用的字段，需要是"表名.字段名"，防止冲突
     *
     * @param string $middleModel
     * @param string $rightModel
     * @param array $middleFields
     * @param array $rightFields
     * @return void
     */
    private static function parseManyToManyQueryFields($middleModel, $rightModel, &$middleFields, &$rightFields)
    {
        $middleFields = [];
        $rightFields = [];

        $middleTable = ModelManager::getTable($middleModel);
        $rightTable = ModelManager::getTable($rightModel);

        foreach(ModelManager::getFieldNames($middleModel) as $name)
        {
            $middleFields[$middleTable . '_' . $name] = $name;
        }

        foreach(ModelManager::getFieldNames($rightModel) as $name)
        {
            $rightFields[$rightTable . '_' . $name] = $name;
        }
    }

    /**
     * 合并多对多查询字段
     *
     * @param string $middleModel
     * @param string $rightModel
     * @param array $middleFields
     * @param array $rightFields
     * @return array
     */
    private static function mergeManyToManyFields($middleTable, $middleFields, $rightTable, $rightFields)
    {
        $result = [];
        foreach($middleFields as $alias => $fieldName)
        {
            $result[] = $middleTable . '.' . $fieldName . ' ' . $alias;
        }
        foreach($rightFields as $alias => $fieldName)
        {
            $result[] = $rightTable . '.' . $fieldName . ' ' . $alias;
        }
        return $result;
    }

    /**
     * 追加到Many列表
     *
     * @param \Imi\Util\ArrayList $manyList
     * @param array $dataList
     * @param array $fields
     * @param string $modelClass
     * @return void
     */
    private static function appendMany($manyList, $dataList, $fields, $modelClass)
    {
        foreach($dataList as $row)
        {
            $tmpRow = [];
            foreach($fields as $alias => $fieldName)
            {
                $tmpRow[$fieldName] = $row[$alias];
            }
            $manyList->append($modelClass::newInstance($tmpRow));
        }
    }
}