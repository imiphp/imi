<?php
namespace Imi\Util;

/**
 * kv存储类，基于SplObjectStorage，支持非对象键
 */
class KVStorage extends \SplObjectStorage
{
/**
	 * object hash => 真实
	 *
	 * @var array
	 */
	private static $map = [];

	/**
	 * scalar resource => object object
	 *
	 * @var array
	 */
	private static $objectMap = [];

	/**
	 * not scalar resource => object object
	 *
	 * @var array
	 */
	private static $otherMap = [];

	/**
	 * object key => object object
	 *
	 * @var array
	 */
	private static $otherToObjectMap = [];

	public function attach($object, $data = null)
	{
		parent::attach($this->parseObject($object), $data);
	}

	public function contains($object)
	{
		return parent::contains($this->parseObject($object));
	}

	public function detach($object)
	{
		parent::detach($this->parseObject($object, false));
	}

	public function getHash($object)
	{
		return parent::getHash($this->parseObject($object));
	}

	public function offsetExists($object)
	{
		return parent::offsetExists($this->parseObject($object));
	}

	public function offsetGet($object)
	{
		return parent::offsetGet($this->parseObject($object, false));
	}

	public function offsetSet($object, $data = null)
	{
		parent::offsetSet($this->parseObject($object), $data);
	}

	public function offsetUnset($object)
	{
		parent::offsetUnset($this->parseObject($object, false));
	}

	/**
	 * 将非对象转为对象，并且是同一个对象
	 * @param object $object
	 * @param boolean $isStore 是否存储该对象
	 * @return object
	 */
	private static function parseObject($object, $isStore = true)
	{
		if(is_object($object))
		{
			return $object;
		}
		if(is_scalar($object))
		{
			if(isset(static::$objectMap[$object]))
			{
				return static::$objectMap[$object];
			}
			else if($isStore)
			{
				return static::$objectMap[$object] = (object)$object;
			}
			else
			{
				return (object)$object;
			}
		}
		else
		{
			// 其它
			if(false !== ($index = array_search($object, static::$otherMap)))
			{
				return static::$otherToObjectMap[$index];	
			}
			else if($isStore)
			{
				$key = spl_object_hash(new \stdclass);
				static::$otherMap[$key] = $object;
				return static::$otherToObjectMap[$key] = (object)$object;
			}
			else
			{
				return (object)$object;
			}
		}
	}
}