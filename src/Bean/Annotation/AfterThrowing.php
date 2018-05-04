<?php
namespace Imi\Bean\Annotation;

/**
 * 在异常时通知
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Bean\Parser\AopParser")
 */
class AfterThrowing extends Base
{
	/**
	 * 允许捕获的异常类列表
	 * @var array
	 */
	public $allow = [];

	/**
	 * 不允许捕获的异常类列表
	 * @var array
	 */
	public $deny = [];
}