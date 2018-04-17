<?php
namespace Imi\Server\Annotation;

/**
 * 监听注解
 * @Annotation
 * @Target("METHOD")
 */
class Listen
{
	/**
	 * 名称
	 * @var string
	 */
	public $name;
}