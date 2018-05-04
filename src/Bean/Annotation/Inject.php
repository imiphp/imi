<?php
namespace Imi\Bean\Annotation;

/**
 * 属性注入
 * @Annotation
 * @Target("PROPERTY")
 * @Parser("Imi\Bean\Parser\AopParser")
 */
class Inject extends Base
{
	/**
	 * 只传一个参数时的参数名
	 * @var string
	 */
	protected $defaultFieldName = 'name';

	/**
	 * Bean名称或类名
	 */
	public $name;

	/**
	 * Bean实例化参数
	 * @var array
	 */
	public $args = [];
}