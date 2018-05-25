<?php
namespace Imi\Util;

/**
 * 通道队列类-支持多进程
 */
abstract class ChannelManager
{
	/**
	 * 是否已初始化过
	 * @var boolean
	 */
	protected static $isInited = false;

	/**
	 * \Swoole\Channel 数组
	 * @var \Swoole\Channel[]
	 */
	protected static $channels = [];

	/**
	 * 初始化
	 * @return void
	 */
	public static function init()
	{
		if(static::$isInited)
		{
			throw new \RuntimeException('ChannelManager can not repeated init');
		}
		foreach(static::$channels as $name => $args)
		{
			static::$channels[$name] = new \Swoole\Channel(...$args);
		}
		static::$isInited = true;
	}

	/**
	 * 增加对象名称
	 * @param string $name
	 * @param int $size 通道占用的内存的尺寸，单位为字节。最小值为64K，最大值没有限制
	 * @return void
	 */
	public static function addName(string $name, int $size = 0)
	{
		if(static::$isInited)
		{
			throw new \RuntimeException('addName failed, ChannelManager was inited');
		}
		static::$channels[$name] = [$size];
	}

	/**
	 * 设置对象名称
	 * @param string[] $names
	 * @return void
	 */
	public static function setNames(array $names)
	{
		if(static::$isInited)
		{
			throw new \RuntimeException('addName failed, ChannelManager was inited');
		}
		foreach($names as $key => $args)
		{
			static::$channels[$key] = $args;
		}
	}

	/**
	 * 获取所有对象名称
	 * @return void
	 */
	public static function getNames()
	{
		return array_keys(static::$channels);
	}

	/**
	 * 获取实例
	 * @param string $name
	 * @return \Swoole\Atomic
	 */
	public static function getInstance(string $name): \Swoole\Channel
	{
		if(!static::$isInited)
		{
			throw new \RuntimeException('getInstance failed, ChannelManager is not initialized');
		}
		if(!isset(static::$channels[$name]))
		{
			throw new \RuntimeException(sprintf('getInstance failed, %s is not found', $name));
		}
		return static::$channels[$name];
	}

	/**
	 * 向通道写入数据
	 * $data可以为任意PHP变量，当$data是非字符串类型时底层会自动进行串化
	 * $data的尺寸超过8K时会启用临时文件存储数据
	 * $data必须为非空变量，如空字符串、空数组、0、null、false
	 * 写入成功返回true
	 * 通道的空间不足时写入失败并返回false
	 * @param string $name
	 * @param mixed $data
	 * @return boolean
	 */
	public static function push(string $name, $data)
	{
		return static::getInstance($name)->push($data);
	}

	/**
	 * 弹出数据
	 * pop方法无需传入任何参数
	 * 当通道内有数据时自动将数据弹出并还原为PHP变量
	 * 当通道内没有任何数据时pop会失败并返回false
	 * @param string $name
	 * @return mixed
	 */
	public static function pop(string $name)
	{
		return static::getInstance($name)->pop();
	}
	
	/**
	 * 获取通道的状态
	 * 返回一个数组，包括2项信息
	 * queue_num 通道中的元素数量
	 * queue_bytes 通道当前占用的内存字节数
	 * @param string $name
	 * @return array
	 */
	public static function stats(string $name): array
	{
		return static::getInstance($name)->stats();
	}
}