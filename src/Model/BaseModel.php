<?php
namespace Imi\Model;

use Imi\Util\Call;
use Imi\Bean\BeanFactory;
use Imi\Util\Interfaces\IArrayable;

/**
 * 模型基类
 */
abstract class BaseModel implements \Iterator, \ArrayAccess, IArrayable, \JsonSerializable
{
	/**
	 * 字段名称
	 * @var array
	 */
	protected $__fieldNames;

	public function __construct($data = [])
	{
		$this->__fieldNames = ModelManager::getFieldNames($this);
		foreach($data as $k => $v)
		{
			$this[$k] = $v;
		}
	}
	
	/**
	 * 实例化当前类
	 * @param mixed ...$args
	 * @return static
	 */
	public static function newInstance(...$args)
	{
		return BeanFactory::newInstance(static::class, ...$args);
	}

	// 实现接口的方法们：

	public function offsetExists($offset)
	{
		$methodName = 'get' . ucfirst($offset);
		return method_exists($this, $methodName) && null !== Call::callUserFunc([$this, $methodName]);
	}

	public function offsetGet($offset)
	{
		$methodName = 'get' . ucfirst($offset);
		if(!method_exists($this, $methodName))
		{
			return null;
		}
		return Call::callUserFunc([$this, $methodName]);
	}

	public function offsetSet($offset, $value)
	{
		$methodName = 'set' . ucfirst($offset);
		if(!method_exists($this, $methodName))
		{
			return;
		}
		Call::callUserFunc([$this, $methodName], $value);
	}

	public function offsetUnset($offset)
	{
		
	}

	/**
	 * 将当前对象作为数组返回
	 * @return array
	 */
	public function toArray(): array
	{
		return \iterator_to_array($this);
	}
	
	public function current()
	{
		return $this[current($this->__fieldNames)] ?? null;
	}

	public function key()
	{
		return current($this->__fieldNames);
	}

	public function next()
	{
		next($this->__fieldNames);
	}

	public function rewind()
	{
		reset($this->__fieldNames);
	}

	public function valid()
	{
		return false !== current($this->__fieldNames);
	}
	
	public function jsonSerialize()
	{
        return $this->toArray();
    }
}