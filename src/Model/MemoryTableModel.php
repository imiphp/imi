<?php
namespace Imi\Model;

use Imi\Util\MemoryTableManager;
use Imi\Model\Parser\ModelParser;

/**
 * Swoole Table 模型
 */
abstract class MemoryTableModel extends BaseModel
{
	/**
	 * 记录的key值
	 * @var string
	 */
	protected $key;

	/**
	 * 查找一条记录
	 * @param string $key
	 * @return static
	 */
	public static function find($key)
	{
		$memoryTableAnnotation = ModelManager::getAnnotation(static::class, 'MemoryTable');
		if(null === $memoryTableAnnotation)
		{
			return null;
		}
		$data = MemoryTableManager::get($memoryTableAnnotation->name, $key);
		if(false === $data)
		{
			return null;
		}
		$data['key'] = $key;
		return static::newInstance($data);
	}

	/**
	 * 查询多条记录
	 * @return static[]
	 */
	public static function select()
	{
		$memoryTableAnnotation = ModelManager::getAnnotation(static::class, 'MemoryTable');
		if(null === $memoryTableAnnotation)
		{
			return null;
		}
		$instance = MemoryTableManager::getInstance($memoryTableAnnotation->name);
		return \iterator_to_array($instance);
	}

	/**
	 * 保存记录
	 * @return void
	 */
	public function save()
	{
		$memoryTableAnnotation = ModelManager::getAnnotation($this, 'MemoryTable');
		if(null === $memoryTableAnnotation)
		{
			return null;
		}
		MemoryTableManager::set($memoryTableAnnotation->name, $this->key, $this->toArray());
	}
	
	/**
	 * 删除记录
	 * @return void
	 */
	public function delete()
	{
		$memoryTableAnnotation = ModelManager::getAnnotation($this, 'MemoryTable');
		if(null === $memoryTableAnnotation)
		{
			return null;
		}
		MemoryTableManager::del($memoryTableAnnotation->name, $this->key);
	}

	/**
	 * 批量删除
	 * @param string ...$keys
	 * @return void
	 */
	public static function deleteBatch(...$keys)
	{
		$memoryTableAnnotation = ModelManager::getAnnotation(static::class, 'MemoryTable');
		if(null === $memoryTableAnnotation)
		{
			return null;
		}
		foreach($keys as $key)
		{
			MemoryTableManager::del($memoryTableAnnotation->name, $key);
		}
	}
	
	/**
	 * 统计数量
	 * @return int
	 */
	public static function count()
	{
		$memoryTableAnnotation = ModelManager::getAnnotation(static::class, 'MemoryTable');
		if(null === $memoryTableAnnotation)
		{
			return null;
		}
		return MemoryTableManager::count($memoryTableAnnotation->name);
	}

	/**
	 * 获取键
	 * @return string
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * 设置键
	 * @param string $key
	 * @return static
	 */
	public function setKey($key)
	{
		$this->key = $key;
		return $this;
	}
}