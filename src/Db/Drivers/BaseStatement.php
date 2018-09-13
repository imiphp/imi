<?php
namespace Imi\Db\Drivers;

use Imi\Util\Defer;
use Imi\Db\Interfaces\IStatement;
use Imi\Bean\BeanFactory;


abstract class BaseStatement implements IStatement
{
	/**
	 * 执行一条预处理语句，延迟执行
	 * @param array $inputParameters
	 * @return Defer
	 */
	public function deferExecute(array $inputParameters = null): Defer
	{
		return $this->parseDefer('execute', $inputParameters);
	}

	/**
	 * 处理延迟调用
	 *
	 * @param string $methodName
	 * @param array $args
	 * @return Defer
	 */
	protected function parseDefer($methodName, ...$args)
	{
		$innerMethodName = '__' . $methodName;
		$db = $this->getDb()->getInstance();
		if(method_exists($db, 'setDefer'))
		{
			$db->setDefer(true);
			$generate = $this->$innerMethodName(...$args);
			$generate->next();
			$callable = function() use($db, $generate){
				$result = $db->recv();
				$generate->send($result);
				return $generate->getReturn();
			};
		}
		else
		{
			$callable = function(){
				return $this->$methodName(...$args);
			};
		}
		return new Defer($callable);
	}
}