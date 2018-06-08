<?php
namespace Imi\Util;

/**
 * 同时可以作为数组和对象访问的类
 */
class LazyArrayObject implements \Iterator, \ArrayAccess
{
	/**
	 * 数据
	 * @var array
	 */
	private $data;

	public function __construct($data = [])
	{
		$this->data = $data;
	}

	public function offsetExists($offset)
	{
		return isset($this->data[$offset]);
	}

	public function offsetGet($offset)
	{
		return $this->data[$offset] ?? null;
	}

	public function offsetSet($offset, $value)
	{
		$this->data[$offset] = $value;
	}

	public function offsetUnset($offset)
	{
		if($this->data[$offset])
		{
			unset($this->data[$offset]);
		}
	}

	public function current()
	{
		current($this->data);
	}

	public function key()
	{
		return key($this->data);
	}

	public function next()
	{
		next($this->data);
	}

	public function rewind()
	{
		reset($this->data);
	}

	public function valid()
	{
		return false !== current($this->data);
	}

	public function __set($name, $value) 
    {
		$this->data[$name] = $value;
	}

	public function __get($name)
	{
		return $this->data[$name] ?? null;
	}

	public function __isset($name)
	{
		return isset($this->data[$name]);
	}

	public function __unset($name)
	{
		if($this->data[$offset])
		{
			unset($this->data[$offset]);
		}
	}
}