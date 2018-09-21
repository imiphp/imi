<?php
namespace Imi\Model\Relation;

use Imi\Util\Imi;
use Imi\Util\Text;
use Imi\Bean\BeanFactory;
use Imi\Model\ModelManager;
use Imi\Db\Query\Where\Where;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Model\Parser\RelationParser;
use Imi\Model\Relation\Struct\OneToOne;
use Imi\Model\Relation\Struct\OneToMany;
use Imi\Model\Relation\Struct\ManyToMany;
use Imi\Model\Relation\Struct\PolymorphicOneToOne;
use Imi\Model\Relation\Struct\PolymorphicOneToMany;
use Imi\Model\Relation\Struct\PolymorphicManyToMany;


abstract class Update
{
    /**
     * 处理更新
     *
     * @param \Imi\Model\Model $model
     * @param string $propertyName
     * @param \Imi\Bean\Annotation\Base $annotation
     * @return void
     */
    public static function parse($model, $propertyName, $annotation)
    {
        if(!$model->$propertyName)
        {
            return;
        }
        $relationParser = RelationParser::getInstance();
        $className = BeanFactory::getObjectClass($model);
        $autoUpdate = $relationParser->getPropertyAnnotation($className, $propertyName, 'AutoUpdate');
        $autoSave = $relationParser->getPropertyAnnotation($className, $propertyName, 'AutoSave');

        if($autoUpdate)
        {
            if(!$autoUpdate->status)
            {
                return;
            }
        }
        else if(!$autoSave || !$autoSave->status)
        {
            return;
        }

        if($annotation instanceof \Imi\Model\Annotation\Relation\OneToOne)
        {
            static::parseByOneToOne($model, $propertyName, $annotation);
        }
        else if($annotation instanceof \Imi\Model\Annotation\Relation\OneToMany)
        {
            static::parseByOneToMany($model, $propertyName, $annotation);
        }
        else if($annotation instanceof \Imi\Model\Annotation\Relation\ManyToMany)
        {
            static::parseByManyToMany($model, $propertyName, $annotation);
        }
        else if($annotation instanceof \Imi\Model\Annotation\Relation\PolymorphicOneToOne)
        {
            static::parseByPolymorphicOneToOne($model, $propertyName, $annotation);
        }
        else if($annotation instanceof \Imi\Model\Annotation\Relation\PolymorphicOneToMany)
        {
            static::parseByPolymorphicOneToMany($model, $propertyName, $annotation);
        }
        else if($annotation instanceof \Imi\Model\Annotation\Relation\PolymorphicManyToMany)
        {
            static::parseByPolymorphicManyToMany($model, $propertyName, $annotation);
        }
    }

    /**
     * 处理一对一更新
     *
     * @param \Imi\Model\Model $model
     * @param string $propertyName
     * @param \Imi\Model\Annotation\Relation\OneToOne $annotation
     * @return void
     */
    public static function parseByOneToOne($model, $propertyName, $annotation)
    {
        $className = BeanFactory::getObjectClass($model);

        $struct = new OneToOne($className, $propertyName, $annotation);
        $leftField = $struct->getLeftField();
        $rightField = $struct->getRightField();

        $model->$propertyName->$rightField = $model->$leftField;
        $model->$propertyName->update();
    }

    /**
     * 处理一对多更新
     *
     * @param \Imi\Model\Model $model
     * @param string $propertyName
     * @param \Imi\Model\Annotation\Relation\OneToMany $annotation
     * @return void
     */
    public static function parseByOneToMany($model, $propertyName, $annotation)
    {
        $className = BeanFactory::getObjectClass($model);

        $struct = new OneToMany($className, $propertyName, $annotation);
        $leftField = $struct->getLeftField();
        $rightField = $struct->getRightField();
        $rightModel = $struct->getRightModel();

        $relationParser = RelationParser::getInstance();
        $autoUpdate = $relationParser->getPropertyAnnotation($className, $propertyName, 'AutoUpdate');
        $autoSave = $relationParser->getPropertyAnnotation($className, $propertyName, 'AutoSave');
        // 是否删除无关数据
        if($autoUpdate)
        {
            $orphanRemoval = $autoUpdate->orphanRemoval;
        }
        else if($autoSave)
        {
            $orphanRemoval = $autoSave->orphanRemoval;
        }
        else
        {
            $orphanRemoval = false;
        }

        if($orphanRemoval)
        {
            // 删除无关联数据
            $pks = ModelManager::getId($rightModel);
            if(is_array($pks))
            {
                if(isset($pks[1]))
                {
                    throw new \RuntimeException(sprintf('%s can not OneToMany, because has more than 1 primary keys', $rightModel));
                }
                $pk = $pks[0];
            }
            else
            {
                $pk = $pks;
            }

            $oldIDs = $rightModel::query()->where($rightField, '=', $model->$leftField)->field($pk)->select()->getColumn();

            $updateIDs = [];
            foreach($model->$propertyName as $row)
            {
                if(null !== $row->$pk)
                {
                    $updateIDs[] = $row->$pk;
                }
                $row->$rightField = $model->$leftField;
                $row->save();
            }

            $deleteIDs = array_diff($oldIDs, $updateIDs);

            if(isset($deleteIDs[0]))
            {
                // 批量删除
                $rightModel::deleteBatch(function(IQuery $query) use($pk, $deleteIDs){
                    $query->whereIn($pk, $deleteIDs);
                });
            }
        }
        else
        {
            // 直接更新
            foreach($model->$propertyName as $row)
            {
                $row->$rightField = $model->$leftField;
                $row->save();
            }
        }
    }

    /**
     * 处理多对多更新
     *
     * @param \Imi\Model\Model $model
     * @param string $propertyName
     * @param \Imi\Model\Annotation\Relation\ManyToMany $annotation
     * @return void
     */
    public static function parseByManyToMany($model, $propertyName, $annotation)
    {
        $className = BeanFactory::getObjectClass($model);

        $struct = new ManyToMany($className, $propertyName, $annotation);
        $middleModel = $struct->getMiddleModel();
        $middleLeftField = $struct->getMiddleLeftField();
        $middleRightField = $struct->getMiddleRightField();
        $leftField = $struct->getLeftField();
        $rightField = $struct->getRightField();

        $relationParser = RelationParser::getInstance();
        $autoUpdate = $relationParser->getPropertyAnnotation($className, $propertyName, 'AutoUpdate');
        $autoSave = $relationParser->getPropertyAnnotation($className, $propertyName, 'AutoSave');
        // 是否删除无关数据
        if($autoUpdate)
        {
            $orphanRemoval = $autoUpdate->orphanRemoval;
        }
        else if($autoSave)
        {
            $orphanRemoval = $autoSave->orphanRemoval;
        }
        else
        {
            $orphanRemoval = false;
        }

        if($orphanRemoval)
        {
            // 删除无关联数据
            $oldRightIDs = $middleModel::query()->where($middleLeftField, '=', $model->$leftField)->field($middleRightField)->select()->getColumn();

            $updateIDs = [];
            foreach($model->$propertyName as $row)
            {
                if(null !== $row->$middleRightField)
                {
                    $updateIDs[] = $row->$middleRightField;
                }
                $row->$middleLeftField = $model->$leftField;
                $row->save();
            }

            $deleteIDs = array_diff($oldRightIDs, $updateIDs);

            if(isset($deleteIDs[0]))
            {
                // 批量删除
                $middleModel::deleteBatch(function(IQuery $query) use($middleLeftField, $middleRightField, $leftField, $model, $deleteIDs){
                    $query->where($middleLeftField, '=', $model->$leftField)->whereIn($middleRightField, $deleteIDs);
                });
            }
        }
        else
        {
            // 直接更新
            foreach($model->$propertyName as $row)
            {
                $row->$middleLeftField = $model->$leftField;
                $row->save();
            }
        }
    }

    /**
     * 模型类（可指定字段）是否包含更新关联关系
     *
     * @param string $className
     * @param string $propertyName
     * @return boolean
     */
    public static function hasUpdateRelation($className, $propertyName = null)
    {
        $relationParser = RelationParser::getInstance();
        $relations = $relationParser->getRelations($className);
        if(null === $relations)
        {
            return false;
        }

        if(null === $propertyName)
        {
            foreach($relations as $name => $annotation)
            {
                $autoUpdate = $relationParser->getPropertyAnnotation($className, $name, 'AutoUpdate');
                $autoSave = $relationParser->getPropertyAnnotation($className, $name, 'AutoSave');
        
                if($autoUpdate)
                {
                    if(!$autoUpdate->status)
                    {
                        continue;
                    }
                }
                else if(!$autoSave || !$autoSave->status)
                {
                    continue;
                }
            }
        }
        else
        {
            $autoUpdate = $relationParser->getPropertyAnnotation($className, $propertyName, 'AutoUpdate');
            $autoSave = $relationParser->getPropertyAnnotation($className, $propertyName, 'AutoSave');
    
            if($autoUpdate)
            {
                if(!$autoUpdate->status)
                {
                    return false;
                }
            }
            else if(!$autoSave || !$autoSave->status)
            {
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 处理多态一对一更新
     *
     * @param \Imi\Model\Model $model
     * @param string $propertyName
     * @param \Imi\Model\Annotation\Relation\PolymorphicOneToOne $annotation
     * @return void
     */
    public static function parseByPolymorphicOneToOne($model, $propertyName, $annotation)
    {
        $className = BeanFactory::getObjectClass($model);

        $struct = new PolymorphicOneToOne($className, $propertyName, $annotation);
        $leftField = $struct->getLeftField();
        $rightField = $struct->getRightField();

        $model->$propertyName->$rightField = $model->$leftField;
        $model->$propertyName->{$annotation->type} = $annotation->typeValue;
        $model->$propertyName->update();
    }

    /**
     * 处理多态一对多更新
     *
     * @param \Imi\Model\Model $model
     * @param string $propertyName
     * @param \Imi\Model\Annotation\Relation\PolymorphicOneToMany $annotation
     * @return void
     */
    public static function parseByPolymorphicOneToMany($model, $propertyName, $annotation)
    {
        $className = BeanFactory::getObjectClass($model);

        $struct = new PolymorphicOneToMany($className, $propertyName, $annotation);
        $leftField = $struct->getLeftField();
        $rightField = $struct->getRightField();
        $rightModel = $struct->getRightModel();

        $relationParser = RelationParser::getInstance();
        $autoUpdate = $relationParser->getPropertyAnnotation($className, $propertyName, 'AutoUpdate');
        $autoSave = $relationParser->getPropertyAnnotation($className, $propertyName, 'AutoSave');
        // 是否删除无关数据
        if($autoUpdate)
        {
            $orphanRemoval = $autoUpdate->orphanRemoval;
        }
        else if($autoSave)
        {
            $orphanRemoval = $autoSave->orphanRemoval;
        }
        else
        {
            $orphanRemoval = false;
        }

        if($orphanRemoval)
        {
            // 删除无关联数据
            $pks = ModelManager::getId($rightModel);
            if(is_array($pks))
            {
                if(isset($pks[1]))
                {
                    throw new \RuntimeException(sprintf('%s can not OneToMany, because has more than 1 primary keys', $rightModel));
                }
                $pk = $pks[0];
            }
            else
            {
                $pk = $pks;
            }

            $oldIDs = $rightModel::query()->where($annotation->type, '=', $annotation->typeValue)->where($rightField, '=', $model->$leftField)->field($pk)->select()->getColumn();

            $updateIDs = [];
            foreach($model->$propertyName as $row)
            {
                if(null !== $row->$pk)
                {
                    $updateIDs[] = $row->$pk;
                }
                $row->$rightField = $model->$leftField;
                $row->{$annotation->type} = $annotation->typeValue;
                $row->save();
            }

            $deleteIDs = array_diff($oldIDs, $updateIDs);

            if(isset($deleteIDs[0]))
            {
                // 批量删除
                $rightModel::deleteBatch(function(IQuery $query) use($pk, $deleteIDs){
                    $query->whereIn($pk, $deleteIDs);
                });
            }
        }
        else
        {
            // 直接更新
            foreach($model->$propertyName as $row)
            {
                $row->$rightField = $model->$leftField;
                $row->save();
            }
        }
    }

    /**
     * 处理多态多对多更新
     *
     * @param \Imi\Model\Model $model
     * @param string $propertyName
     * @param \Imi\Model\Annotation\Relation\PolymorphicManyToMany $annotation
     * @return void
     */
    public static function parseByPolymorphicManyToMany($model, $propertyName, $annotation)
    {
        $className = BeanFactory::getObjectClass($model);

        $struct = new PolymorphicManyToMany($className, $propertyName, $annotation);
        $middleModel = $struct->getMiddleModel();
        $middleLeftField = $struct->getMiddleLeftField();
        $middleRightField = $struct->getMiddleRightField();
        $leftField = $struct->getLeftField();
        $rightField = $struct->getRightField();

        $relationParser = RelationParser::getInstance();
        $autoUpdate = $relationParser->getPropertyAnnotation($className, $propertyName, 'AutoUpdate');
        $autoSave = $relationParser->getPropertyAnnotation($className, $propertyName, 'AutoSave');
        // 是否删除无关数据
        if($autoUpdate)
        {
            $orphanRemoval = $autoUpdate->orphanRemoval;
        }
        else if($autoSave)
        {
            $orphanRemoval = $autoSave->orphanRemoval;
        }
        else
        {
            $orphanRemoval = false;
        }

        if($orphanRemoval)
        {
            // 删除无关联数据
            $oldRightIDs = $middleModel::query()->where($annotation->type, '=', $annotation->typeValue)->where($middleLeftField, '=', $model->$leftField)->field($middleRightField)->select()->getColumn();

            $updateIDs = [];
            foreach($model->$propertyName as $row)
            {
                if(null !== $row->$middleRightField)
                {
                    $updateIDs[] = $row->$middleRightField;
                }
                $row->$middleLeftField = $model->$leftField;
                $row->{$annotation->type} = $annotation->typeValue;
                $row->save();
            }

            $deleteIDs = array_diff($oldRightIDs, $updateIDs);

            if(isset($deleteIDs[0]))
            {
                // 批量删除
                $middleModel::deleteBatch(function(IQuery $query) use($middleLeftField, $middleRightField, $leftField, $model, $deleteIDs, $annotation){
                    $query->where($annotation->type, '=', $annotation->typeValue)->where($middleLeftField, '=', $model->$leftField)->whereIn($middleRightField, $deleteIDs);
                });
            }
        }
        else
        {
            // 直接更新
            foreach($model->$propertyName as $row)
            {
                $row->$middleLeftField = $model->$leftField;
                $row->{$annotation->type} = $annotation->typeValue;
                $row->save();
            }
        }
    }
}