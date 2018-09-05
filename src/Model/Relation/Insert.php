<?php
namespace Imi\Model\Relation;

use Imi\Util\Imi;
use Imi\Util\Text;
use Imi\Bean\BeanFactory;
use Imi\Model\ModelManager;
use Imi\Model\Parser\RelationParser;
use Imi\Model\Relation\Struct\OneToOne;


abstract class Insert
{
	/**
	 * 处理插入
	 *
	 * @param \Imi\Model\Model $model
	 * @param string $propertyName
	 * @param \Imi\Bean\Annotation\Base $annotation
	 * @return void
	 */
	public static function parse($model, $propertyName, $annotation)
	{
		if($annotation instanceof \Imi\Model\Annotation\Relation\OneToOne)
		{
			static::parseByOneToOne($model, $propertyName, $annotation);
		}
	}

	/**
	 * 处理一对一插入
	 *
	 * @param \Imi\Model\Model $model
	 * @param string $propertyName
	 * @return void
	 */
	public static function parseByOneToOne($model, $propertyName)
	{
		if(!$model->$propertyName)
		{
			return;
		}
		$relationParser = RelationParser::getInstance();
		$className = BeanFactory::getObjectClass($model);
		$autoInsert = $relationParser->getPropertyAnnotation($className, $propertyName, 'AutoInsert');
		$autoSave = $relationParser->getPropertyAnnotation($className, $propertyName, 'AutoSave');
		if(!$autoInsert || $autoInsert->status || ($autoSave && $autoSave->status))
		{
			$struct = new OneToOne($className, $propertyName);
			$leftField = $struct->getLeftField();
			$rightField = $struct->getRightField();

			$model->$propertyName->$rightField = $model->$leftField;
			$model->$propertyName->insert();
		}
	}
}