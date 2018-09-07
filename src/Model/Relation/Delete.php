<?php
namespace Imi\Model\Relation;

use Imi\Util\Imi;
use Imi\Util\Text;
use Imi\Bean\BeanFactory;
use Imi\Model\ModelManager;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Model\Parser\RelationParser;
use Imi\Model\Relation\Struct\OneToOne;
use Imi\Model\Relation\Struct\OneToMany;


abstract class Delete
{
	/**
	 * 处理删除
	 *
	 * @param \Imi\Model\Model $model
	 * @param string $propertyName
	 * @param \Imi\Model\Annotation\Relation\OneToOne $annotation
	 * @return void
	 */
	public static function parse($model, $propertyName, $annotation)
	{
		if($annotation instanceof \Imi\Model\Annotation\Relation\OneToOne)
		{
			static::parseByOneToOne($model, $propertyName, $annotation);
		}
		else if($annotation instanceof \Imi\Model\Annotation\Relation\OneToMany)
		{
			static::parseByOneToMany($model, $propertyName, $annotation);
		}
	}

	/**
	 * 处理一对一删除
	 *
	 * @param \Imi\Model\Model $model
	 * @param string $propertyName
	 * @param \Imi\Model\Annotation\Relation\OneToOne $annotation
	 * @return void
	 */
	public static function parseByOneToOne($model, $propertyName, $annotation)
	{
		if(!$model->$propertyName)
		{
			return;
		}
		$relationParser = RelationParser::getInstance();
		$className = BeanFactory::getObjectClass($model);
		$autoDelete = $relationParser->getPropertyAnnotation($className, $propertyName, 'AutoDelete');

		if(!$autoDelete || !$autoDelete->status)
		{
			return;
		}

		$struct = new OneToOne($className, $propertyName, $annotation);
		$leftField = $struct->getLeftField();
		$rightField = $struct->getRightField();

		$model->$propertyName->$rightField = $model->$leftField;
		$model->$propertyName->delete();
	}

	/**
	 * 处理一对多删除
	 *
	 * @param \Imi\Model\Model $model
	 * @param string $propertyName
	 * @param \Imi\Model\Annotation\Relation\OneToMany $annotation
	 * @return void
	 */
	public static function parseByOneToMany($model, $propertyName, $annotation)
	{
		if(!$model->$propertyName)
		{
			return;
		}
		$relationParser = RelationParser::getInstance();
		$className = BeanFactory::getObjectClass($model);

		$autoDelete = $relationParser->getPropertyAnnotation($className, $propertyName, 'AutoDelete');

		if(!$autoDelete || !$autoDelete->status)
		{
			return;
		}

		$struct = new OneToMany($className, $propertyName, $annotation);
		$leftField = $struct->getLeftField();
		$rightField = $struct->getRightField();
		$rightModel = $struct->getRightModel();

		$rightModel::deleteBatch(function(IQuery $query) use($model, $leftField, $rightField){
			$query->where($rightField, '=', $model->$leftField);
		});
	}
}