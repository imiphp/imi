<?php
namespace Imi\Model;

use Imi\Util\Imi;
use Imi\Util\Text;
use Imi\Bean\BeanFactory;
use Imi\Model\Relation\Query;
use Imi\Model\Parser\RelationParser;

abstract class ModelRelationManager
{
	/**
	 * 初始化模型
	 *
	 * @param \Imi\Model\Model $model
	 * @return void
	 */
	public static function initModel($model)
	{
		foreach(RelationParser::getInstance()->getRelations(BeanFactory::getObjectClass($model)) as $propertyName => $propertyItem)
		{
			if(null !== $model[$propertyName])
			{
				continue;
			}
			foreach($propertyItem as $type => $list)
			{
				foreach($list as $annotation)
				{
					Query::init($model, $propertyName, $annotation);
				}
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
		$relationParser = RelationParser::getInstance();
		$className = BeanFactory::getObjectClass($model);
		foreach($relationParser->getRelations($className) as $propertyName => $propertyItem)
		{
			if(!$model->$propertyName)
			{
				continue;
			}
			$autoInsert = $relationParser->getPropertyAnnotation($className, $propertyName, 'AutoInsert');
			if(!$autoInsert || $autoInsert->status)
			{
				$joinFrom = $relationParser->getPropertyAnnotation($className, $propertyName, 'JoinFrom');
				$joinTo = $relationParser->getPropertyAnnotation($className, $propertyName, 'JoinTo');
		
				if($joinFrom)
				{
					$leftField = $joinFrom->field;
				}
				else
				{
					$leftField = ModelManager::getFirstId($model);
				}
		
				if($joinTo)
				{
					$rightField = $joinTo->field;
				}
				else
				{
					$rightField = Text::toUnderScoreCase(Imi::getClassShortName($className)) . '_id';
				}

				$model->$propertyName->$rightField = $model->$leftField;
				$model->$propertyName->insert();
			}
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
		return array_keys(RelationParser::getInstance()->getData()[$class]['properties'] ?? []);
	}
}