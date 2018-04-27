<?php
namespace Imi\Util;

/**
 * kv存储类，基于SplObjectStorage，支持非对象键
 */
class KVStorage extends \SplObjectStorage
{
	private $objectMap = [];

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
		return parent::offsetGet($this->parseObject($object));
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
	private function parseObject($object, $isStore = true)
	{
		if(is_object($object))
		{
			return $object;
		}
		if(!isset($this->objectMap[$object]))
		{
			$this->objectMap[$object] = (object)$object;
		}
		$result = $this->objectMap[$object];
		if(!$isStore)
		{
			unset($this->objectMap[$object]);
		}
		return $result;
	}
}