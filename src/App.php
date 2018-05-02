<?php
namespace Imi;

use Imi\Event\Event;
use Imi\Bean\Annotation;
use Imi\Server\Http\Server;
use Imi\Main\Helper as MainHelper;
use Imi\Bean\Container;

abstract class App
{
	/**
	 * 服务器对象数组
	 * @var array
	 */
	private static $servers;

	/**
	 * 注解类
	 * @var \Imi\Bean\Annotation
	 */
	private static $annotation;

	/**
	 * 应用命名空间
	 * @var string
	 */
	private static $namespace;

	/**
	 * 容器
	 * @var \Imi\Bean\Container
	 */
	private static $container;

	/**
	 * 框架运行入口
	 * @param string $namespace 应用命名空间
	 * @return void
	 */
	public static function run($namespace)
	{
		static::$namespace = $namespace;
		static::initFramework();
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
	 * 框架初始化
	 * @return void
	 */
	private static function initFramework()
	{
		static::$container = new Container;
		// 框架主类执行
		MainHelper::getMain('Imi')->init();
		// 应用主类执行
		MainHelper::getMain(static::$namespace)->init();
		// 服务器主类执行

		// 注解处理
		static::$annotation = new Annotation;
		static::$annotation->init();

	}

	/**
	 * 创建服务器对象们
	 * @return void
	 */
	private static function createServers()
	{
		// 创建服务器对象们前置操作
		Event::trigger('IMI.SERVERS.CREATE.BEFORE');
		$config = MainHelper::getMain(static::$namespace)->getConfig();
		if(!isset($config['mainServer']))
		{
			throw new \Exception('config.mainServer not found');
		}
		// 主服务器
		static::createServer('main', $config['mainServer']);
		// 创建监听子服务器端口
		if(isset($config['subServers']))
		{
			foreach($config['subServers'] as $name => $config)
			{
				static::createServer($name, $config, true);
			}
		}
		// 创建服务器对象们后置操作
		Event::trigger('IMI.SERVERS.CREATE.AFTER');
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
		// $server = new $serverClassName($config, $isSubServer);
		$server = static::getBean($serverClassName, $config, $isSubServer);
		static::$servers[$name] = $server;
		// 创建服务器对象前置后作
		Event::trigger('IMI.SERVER.CREATE.AFTER', [
			'name'			=>	$name,
			'config'		=>	$config,
			'isSubServer'	=>	$isSubServer,
		]);
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

	/**
	 * 获取应用命名空间
	 * @return string
	 */
	public static function getNamespace()
	{
		return static::$namespace;
	}

	/**
	 * 获取Bean对象
	 * @param string $name
	 * @return mixed
	 */
	public static function getBean($name, ...$params)
	{
		return static::$container->getBean($name, ...$params);
	}
}