<?php
namespace Imi\Model;

use Imi\Util\Imi;
use Imi\Util\Text;
use Imi\Bean\BeanFactory;
use Imi\Model\Relation\Query;
use Imi\Model\Relation\Delete;
use Imi\Model\Relation\Insert;
use Imi\Model\Relation\Update;
use Imi\Model\Parser\RelationParser;
use Imi\Model\Annotation\Relation\ManyToMany;

abstract class ModelRelationManager
{
    private static $relationFieldsNames = [];

    /**
     * 初始化模型
     *
     * @param \Imi\Model\Model $model
     * @return void
     */
    public static function initModel($model)
    {
        foreach(RelationParser::getInstance()->getRelations(BeanFactory::getObjectClass($model)) as $propertyName => $annotation)
        {
            if(null !== $model[$propertyName])
            {
                continue;
            }
            Query::init($model, $propertyName, $annotation);
        }
    }

    /**
     * 查询模型指定关联
     *
     * @param \Imi\Model\Model $model
     * @param string ...$names
     * @return void
     */
    public static function queryModelRelations($model, ...$names)
    {
        $relations = RelationParser::getInstance()->getRelations(BeanFactory::getObjectClass($model));
        foreach($names as $name)
        {
            if(isset($relations[$name]))
            {
                Query::init($model, $name, $relations[$name], true);
            }
        }
    }

    /**
     * 插入模型
     *
     * @param \Imi\Model\Model $model
     * @return void
     */
    public static function insertModel($model)
    {
        foreach(RelationParser::getInstance()->getRelations(BeanFactory::getObjectClass($model)) as $propertyName => $annotation)
        {
            if(null === $model[$propertyName])
            {
                continue;
            }
            Insert::parse($model, $propertyName, $annotation);
        }
    }

    /**
     * 更新模型
     *
     * @param \Imi\Model\Model $model
     * @return void
     */
    public static function updateModel($model)
    {
        foreach(RelationParser::getInstance()->getRelations(BeanFactory::getObjectClass($model)) as $propertyName => $annotation)
        {
            if(null === $model[$propertyName])
            {
                continue;
            }
            Update::parse($model, $propertyName, $annotation);
        }
    }

    /**
     * 删除模型
     *
     * @param \Imi\Model\Model $model
     * @return void
     */
    public static function deleteModel($model)
    {
        foreach(RelationParser::getInstance()->getRelations(BeanFactory::getObjectClass($model)) as $propertyName => $annotation)
        {
            if(null === $model[$propertyName])
            {
                continue;
            }
            Delete::parse($model, $propertyName, $annotation);
        }
    }

    /**
     * 获取当前模型关联字段名数组
     * @param string|object $object
     * @return string[]
     */
    public static function getRelationFieldNames($object)
    {
        $class = BeanFactory::getObjectClass($object);
        if(!isset(static::$relationFieldsNames[$class]))
        {
            $relations = RelationParser::getInstance()->getData()[$class]['relations'] ?? [];
            $result = array_keys($relations);
            foreach($relations as $annotation)
            {
                if($annotation instanceof ManyToMany && $annotation->rightMany)
                {
                    $result[] = $annotation->rightMany;
                }
            }
            static::$relationFieldsNames[$class] = $result;
        }
        return static::$relationFieldsNames[$class];
    }
}