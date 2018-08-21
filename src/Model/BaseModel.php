<?php
namespace Imi\Model;

use Imi\Util\Text;
use Imi\Event\TEvent;
use Imi\Bean\BeanFactory;
use Imi\Util\ClassObject;
use Imi\Model\Event\ModelEvents;
use Imi\Util\Interfaces\IArrayable;
use Imi\Util\LazyArrayObject;

/**
 * 模型基类
 */
abstract class BaseModel implements \Iterator, \ArrayAccess, IArrayable, \JsonSerializable
{
	use TEvent;

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

	/**
	 * 从存储中读取出来的原始数据
	 *
	 * @var array
	 */
	protected $__originValues = [];

	public function __construct($data = [])
	{
		if(!ClassObject::isAnymous($this))
		{
			$this->__init();
		}
	}

	public function __init($data = [])
	{
		$this->__originValues = $data;
		$this->__fieldNames = ModelManager::getFieldNames($this);

		$data = new LazyArrayObject($data);

		// 初始化前
		$this->trigger(ModelEvents::BEFORE_INIT, [
			'model'	=>	$this,
			'data'	=>	$data,
		], $this, \Imi\Model\Event\Param\InitEventParam::class);

		foreach($data as $k => $v)
		{
			$this[$k] = $v;
		}

		// 初始化后
		$this->trigger(ModelEvents::AFTER_INIT, [
			'model'	=>	$this,
			'data'	=>	$data,
		], $this, \Imi\Model\Event\Param\InitEventParam::class);
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
		// 数据库bit类型字段处理
		$column = ModelManager::getPropertyAnnotation($this, $offset, 'Column');
		if(null === $column)
		{
			$column = ModelManager::getPropertyAnnotation($this, $this->__getCamelName($offset), 'Column');
		}
		if(null !== $column)
		{
			if('bit' === $column->type)
			{
				$value = (1 == $value || chr(1) == $value);
			}
		}

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
	 * 从一个数组赋值到当前模型
	 *
	 * @param array $data
	 * @return void
	 */
	public function set(array $data)
	{
		foreach($data as $k => $v)
		{
			$this[$k] = $v;
		}
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