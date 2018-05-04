<?php
namespace Imi\Bean\Annotation;

/**
 * 切入点
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Bean\Parser\AopParser")
 */
class PointCut extends Base
{
	/**
	 * 允许的切入点
	 * @var array
	 */
	public $allow = [];

	/**
	 * 不允许的切入点，即使包含中有的，也可以被排除
	 * @var array
	 */
	public $deny = [];
}