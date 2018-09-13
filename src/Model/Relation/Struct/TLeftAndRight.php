<?php
namespace Imi\Model\Relation\Struct;

use Imi\Util\Imi;
use Imi\Util\Text;
use Imi\Bean\BeanFactory;
use Imi\Model\ModelManager;
use Imi\Model\Parser\RelationParser;

trait TLeftAndRight
{
	/**
	 * 左侧表字段
	 *
	 * @var string
	 */
	private $leftField;
	
	/**
	 * 右侧表字段
	 *
	 * @var string
	 */
	private $rightField;

	/**
	 * 右侧模型类
	 *
	 * @var string
	 */
	private $rightModel;

	/**
	 * 初始化左右关联
	 *
	 * @param \Imi\Model\Model $model
	 * @param string $propertyName
	 * @param \Imi\Model\Annotation\Relation\OneToOne $annotation
	 * @return void
	 */
	public function initLeftAndRight($className, $propertyName, $annotation)
	{
		$relationParser = RelationParser::getInstance();

		if(class_exists($annotation->model))
		{
			$this->rightModel = $annotation->model;
		}
		else
		{
			$this->rightModel = Imi::getClassNamespace($className) . '\\' . $annotation->model;
		}
		
		$joinFrom = $relationParser->getPropertyAnnotation($className, $propertyName, 'JoinFrom');
		$joinTo = $relationParser->getPropertyAnnotation($className, $propertyName, 'JoinTo');

		if($joinFrom)
		{
			$this->leftField = $joinFrom->field;
		}
		else
		{
			$this->leftField = ModelManager::getFirstId($className);
		}

		if($joinTo)
		{
			$this->rightField = $joinTo->field;
		}
		else
		{
			$this->rightField = Text::toUnderScoreCase(Imi::getClassShortName($className)) . '_id';
		}
	}

	/**
	 * Get the value of leftField
	 */ 
	public function getLeftField()
	{
		return $this->leftField;
	}

	/**
	 * Get the value of rightField
	 */ 
	public function getRightField()
	{
		return $this->rightField;
	}

	/**
	 * Get 右侧模型类
	 *
	 * @return  string
	 */ 
	public function getRightModel()
	{
		return $this->rightModel;
	}
}