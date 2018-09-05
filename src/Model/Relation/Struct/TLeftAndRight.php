<?php
namespace Imi\Model\Relation\Struct;

use Imi\Util\Imi;
use Imi\Util\Text;
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

	public function initLeftAndRight($className, $propertyName)
	{
		$relationParser = RelationParser::getInstance();
		
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
}