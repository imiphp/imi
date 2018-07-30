<?php
namespace Imi\Model;

use Imi\Bean\BeanFactory;
use Imi\Util\Interfaces\IArrayable;
use Imi\Util\Text;
use Imi\Util\ClassObject;

/**
 * 模型基类
 */
abstract class BaseModel implements \Iterator, \ArrayAccess, IArrayable, \JsonSerializable
{
	/**
	 * 数据库原始字段名称
	 * @var array
	 */
	protected $__fieldNames;

	/**
	 * 驼峰缓存
	 * @var array
	 */
	protected $__camelCache = [];

	public function __construct($data = [])
	{
		if(!ClassObject::isAnymous($this))
		{
			$this->__init();
		}
	}

	public function __init($data = [])
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
		$methodName = 'get' . ucfirst($this->__getCamelName($offset));
		return method_exists($this, $methodName) && null !== call_user_func([$this, $methodName]);
	}

	public function offsetGet($offset)
	{
		$methodName = 'get' . ucfirst($this->__getCamelName($offset));
		if(!method_exists($this, $methodName))
		{
			return null;
		}
		return call_user_func([$this, $methodName]);
	}

	public function offsetSet($offset, $value)
	{
		$methodName = 'set' . ucfirst($this->__getCamelName($offset));
		if(!method_exists($this, $methodName))
		{
			return;
		}
		call_user_func([$this, $methodName], $value);
	}

	public function offsetUnset($offset)
	{
		
	}

	public function __get($name)
	{
		return $this[$name];
	}

	public function __set($name, $value)
	{
		$this[$name] = $value;
	}

	public function __isset($name)
	{
		return isset($this[$name]);
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
		return $this[$this->__getFieldName(current($this->__fieldNames))] ?? null;
	}

	public function key()
	{
		return $this->__getFieldName(current($this->__fieldNames));
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
		return false !== $this->__getFieldName(current($this->__fieldNames));
	}
	
	public function jsonSerialize()
	{
        return $this->toArray();
	}
	
	/**
	 * 获取驼峰命名
	 * @param string $name
	 * @return string
	 */
	protected function __getCamelName($name)
	{
		if(!isset($this->__camelCache[$name]))
		{
			$this->__camelCache[$name] = Text::toCamelName($name);
		}
		return $this->__camelCache[$name];
	}

	protected function __getFieldName($fieldName)
	{
		if(false === $fieldName)
		{
			return false;
		}
		if(ModelManager::isCamel($this))
		{
			return $this->__getCamelName($fieldName);
		}
		else
		{
			return $fieldName;
		}
	}
}