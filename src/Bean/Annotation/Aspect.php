<?php
namespace Imi\Bean\Annotation;

/**
 * 切面注解
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Bean\Parser\AopParser")
 */
class Aspect extends Base
{
	/**
	 * 优先级，越大越先执行
	 * @var int
	 */
	public $priority = 0;
}