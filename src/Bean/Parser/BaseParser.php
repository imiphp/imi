<?php
namespace Imi\Bean\Parser;

use Imi\Util\TSingleton;

abstract class BaseParser implements IParser
{
	use TSingleton;

	/**
	 * 数据
	 * @var array
	 */
	protected $data = [];
	
	/**
	 * 获取数据
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * 是否子类作为单独实例
	 * @return boolean
	 */
	protected static function isChildClassSingleton()
	{
		return true;
	}
}