<?php
namespace Imi\Model;

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