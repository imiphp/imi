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
		ServerManage::getServer('main')->getSwooleServer()->start();
	}

	/**
	 * 框架初始化
	 * @return void
	 */
	private static function initFramework()
	{
		static::$container = new Container;
		// 框架主类执行
		MainHelper::getMain('Imi', 'Imi')->init();
		// 项目主类执行
		$main = MainHelper::getMain(static::$namespace, 'app');
		$main->init();
		// 服务器主类执行
		$servers = array_merge(['main'=>Config::get('@app.mainServer')], Config::get('@app.subServers', []));
		foreach($servers as $serverName => $item)
		{
			$serverMain = MainHelper::getMain($item['namespace'], 'server_' . $serverName);
			null !== $serverMain and $serverMain->init();
		}
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
		$mainServer = Config::get('@app.mainServer');
		if(null === $mainServer)
		{
			throw new \Exception('config.mainServer not found');
		}
		// 主服务器
		ServerManage::createServer('main', $mainServer);
		// 创建监听子服务器端口
		$subServers = Config::get('@app.subServers', []);
		foreach($subServers as $name => $config)
		{
			ServerManage::createServer($name, $config, true);
		}
		// 创建服务器对象们后置操作
		Event::trigger('IMI.SERVERS.CREATE.AFTER');
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
		return static::$container->get($name, ...$params);
	}
}