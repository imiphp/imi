<?php
namespace Imi\Db\Drivers\CoroutineMysql;

use Imi\Bean\BeanFactory;
use Imi\Util\LazyArrayObject;

/**
 * Statement fetch处理器
 */
class StatementFetchParser
{
	/**
	 * 处理行
	 * @param array $row
	 * @param int $fetchStyle
	 * @param array $columnBinds
	 * @return mixed
	 */
	public function parseRow(array $row, int $fetchStyle, array $columnBinds = [])
	{
		switch($fetchStyle)
		{
			case \PDO::FETCH_ASSOC:
				return $row;
			case \PDO::FETCH_BOTH:
				return array_values($row) + $row;
			case \PDO::FETCH_BOUND:
				foreach($row as $key => $value)
				{
					if(isset($columnBinds[$key]))
					{
						$columnBinds[$key]['param'] = $value;
					}
				}
				return true;
			case \PDO::FETCH_CLASS:
				if(Bit::has($fetchStyle, \PDO::FETCH_CLASSTYPE))
				{
					$className = reset($row);
				}
				else
				{
					$className = 'stdClass';
				}
				$object = BeanFactory::newInstance($className);
				foreach($row as $key => $value)
				{
					$object->$key = $value;
				}
				return $object;
			case \PDO::FETCH_LAZY:
				return new LazyArrayObject($row);
			case \PDO::FETCH_NUM:
				return array_values($row);
			case \PDO::FETCH_OBJ:
				$className = 'stdClass';
				$object = BeanFactory::newInstance($className);
				foreach($row as $key => $value)
				{
					$object->$key = $value;
				}
				return $object;
			default:
				throw new \InvalidArgumentException('Statement fetch $fetchStyle can not use ' . $fetchStyle);
				break;
		}
	}
}