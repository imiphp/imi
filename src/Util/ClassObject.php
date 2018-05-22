<?php
namespace Imi\Util;

abstract class ClassObject
{
	/**
	 * 是否是匿名类
	 * @param object $object
	 * @return boolean
	 */
	public static function isAnymous($object)
	{
		return strpos(get_class($object), 'class@anonymous') >= 0;
	}
}