<?php
namespace Imi;

use Imi\Event\Event;

abstract class ServerManage
{
	/**
	 * 服务器对象数组
	 * @var array
	 */
	private static $servers = [];

	/**
	 * 获取服务器数组
	 * @return array
	 */
	public static function getServers()
	{
		return static::$servers;
	}

	/**
	 * 获取服务器对象
	 * @param string $name
	 * @return \Imi\Server\Base
	 */
	public static function getServer($name)
	{
		if(isset(static::$servers[$name]))
		{
			return static::$servers[$name];
		}
		else
		{
			return null;
		}
	}

	/**
	 * 创建服务器对象
	 * @param string $name
	 * @param array $config
	 * @param bool $subServer 是否为子服务器
	 * @return void
	 */
	public static function createServer($name, $config, $isSubServer = false)
	{
		// 创建服务器对象前置操作
		Event::trigger('IMI.SERVER.CREATE.BEFORE', [
			'name'			=>	$name,
			'config'		=>	$config,
			'isSubServer'	=>	$isSubServer,
		]);
		// 服务器类名
		$serverClassName = 'Imi\Server\\' . $config['type'] . '\Server';
		// 主服务器实例对象
		$server = App::getBean($serverClassName, $name, $config, $isSubServer);
		static::$servers[$name] = $server;
		// 创建服务器对象前置后作
		Event::trigger('IMI.SERVER.CREATE.AFTER', [
			'name'			=>	$name,
			'config'		=>	$config,
			'isSubServer'	=>	$isSubServer,
		]);
	}
}