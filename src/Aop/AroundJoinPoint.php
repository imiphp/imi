<?php
namespace Imi\Aop;

class AroundJoinPoint extends JoinPoint
{
	/**
	 * process调用的方法
	 * @var callable
	 */
	private $nextProceed;

	public function __construct($type, $method, $args, $target, $_this, $nextProceed)
	{
		parent::__construct($type, $method, $args, $target, $_this);
		$this->nextProceed = $nextProceed;
	}

	/**
	 * 调用下一个方法
	 * @return mixed
	 */
	public function proceed()
	{
		return call_user_func($this->nextProceed, $this->getArgs());
	}
}