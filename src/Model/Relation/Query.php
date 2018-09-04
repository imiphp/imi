<?php
namespace Imi\Model\Relation;

use Imi\Util\Imi;
use Imi\Util\Text;
use Imi\Bean\BeanFactory;
use Imi\Model\ModelManager;
use Imi\Model\Parser\RelationParser;
use Imi\Model\Annotation\Relation\OneToOne;


abstract class Query
{
	/**
	 * 初始化
	 *
	 * @param \Imi\Model\Model $model
	 * @param string $propertyName
	 * @param \Imi\Bean\Annotation\Base $annotation
	 * @return void
	 */
	public static function init($model, $propertyName, $annotation)
	{
		if($annotation instanceof OneToOne)
		{
			static::initByOneToOne($model, $propertyName, $annotation);
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
		$relationParser = RelationParser::getInstance();
		$className = BeanFactory::getObjectClass($model);

		$joinFrom = $relationParser->getPropertyAnnotation($className, $propertyName, 'JoinFrom');
		$joinTo = $relationParser->getPropertyAnnotation($className, $propertyName, 'JoinTo');

		$toModel = $annotation->model;

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

		if(class_exists($annotation->model))
		{
			$modelClass = $annotation->model;
		}
		else
		{
			$modelClass = Imi::getClassNamespace($className) . '\\' . $annotation->model;
		}

		$rightModel = $modelClass::query()->where($rightField, '=', $model->$leftField)->select()->get();

		$model->$propertyName = $rightModel;
	}
}