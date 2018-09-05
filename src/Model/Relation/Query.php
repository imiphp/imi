<?php
namespace Imi\Model\Relation;

use Imi\Util\Imi;
use Imi\Util\Text;
use Imi\Bean\BeanFactory;
use Imi\Model\ModelManager;
use Imi\Model\Parser\RelationParser;
use Imi\Model\Relation\Struct\OneToOne;


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
		if($annotation instanceof \Imi\Model\Annotation\Relation\OneToOne)
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

		$toModel = $annotation->model;

		if(class_exists($annotation->model))
		{
			$modelClass = $annotation->model;
		}
		else
		{
			$modelClass = Imi::getClassNamespace($className) . '\\' . $annotation->model;
		}

		$struct = new OneToOne($className, $propertyName);
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
}