<?php
namespace Imi;

use Imi\Server\Http\Server;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Yurun\Util\Event;

abstract class Manager
{
	/**
	 * 配置
	 * @var array
	 */
	private static $config;

	/**
	 * 服务器对象数组
	 * @var array
	 */
	private static $servers;

	/**
	 * 初始化配置
	 * @param array $config
	 * @return void
	 */
	public static function init($config)
	{
		static::$config = $config;
		// 注解自动加载支持
		foreach(spl_autoload_functions() as $func)
		{
			AnnotationRegistry::registerLoader($func);
		}
	}

	/**
	 * 框架运行入口
	 * @return void
	 */
	public static function run()
	{
		// 框架运行事件
		Event::trigger('IMI.RUN');
		static::createServers();
		// 主服务器启动前事件
		Event::trigger('IMI.MAIN_SERVER.START.BEFORE');
		static::getServer('main')->getSwooleServer()->start();
		// 主服务器启动完毕事件
		Event::trigger('IMI.MAIN_SERVER.START.AFTER');
	}

	/**
	 * 创建服务器对象们
	 * @return void
	 */
	private static function createServers()
	{
		if(!isset(static::$config['mainServer']))
		{
			throw new \Exception('config.mainServer not found');
		}
		// 主服务器
		static::createServer('main', static::$config['mainServer']);
		// 创建监听子服务器端口
		if(isset(static::$config['subServers']))
		{
			foreach(static::$config['subServers'] as $name => $config)
			{
				static::createServer($name, $config, true);
			}
		}
	}

	/**
	 * 创建服务器对象
	 * @param string $name
	 * @param array $config
	 * @param bool $subServer 是否为子服务器
	 * @return void
	 */
	private static function createServer($name, $config, $isSubServer = false)
	{
		// 创建服务器对象前置操作
		Event::trigger('IMI.SERVER.CREATE.BEFORE', $name, $config, $isSubServer);
		// 服务器类名
		$serverClassName = 'Imi\Server\\' . $config['type'] . '\Server';
		// 主服务器实例对象
		$server = new $serverClassName($config, $isSubServer);
		static::$servers[$name] = $server;
		// 创建服务器对象前置后作
		Event::trigger('IMI.SERVER.CREATE.AFTER', $name, $config, $isSubServer);
	}

	/**
	 * 获取框架配置
	 * @return array
	 */
	public static function getConfig()
	{
		return static::$config;
	}

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
}
