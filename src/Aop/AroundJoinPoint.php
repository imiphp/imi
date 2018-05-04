<?php
namespace Imi\Aop;

use Imi\Util\Call;

class AroundJoinPoint extends JoinPoint
{
	/**
	 * process调用的方法
	 * @var callable
	 */
	private $nextProceed;

	public function __construct($type, $method, $args, $target, $_this, $nextProceed)
	{
		parent::__construct(...func_get_args());
		$this->nextProceed = $nextProceed;
	}

	/**
	 * 调用下一个方法
	 * @return mixed
	 */
	public function proceed()
	{
		return Call::callUserFunc($this->nextProceed, $this->getArgs());
	}
}