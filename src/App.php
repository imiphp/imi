<?php
namespace Imi;

use Imi\Event\Event;
use Imi\Log\LogLevel;
use Imi\Main\BaseMain;
use Imi\Bean\Container;
use Imi\Util\Coroutine;
use Imi\Bean\Annotation;
use Imi\Pool\PoolConfig;
use Imi\Pool\PoolManager;
use Imi\Cache\CacheManager;
use Imi\Server\Http\Server;
use Imi\Main\Helper as MainHelper;
use Imi\Util\CoroutineChannelManager;

abstract class App
{
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
	 * 框架是否已初始化
	 * @var boolean
	 */
	private static $isInited = false;

	/**
	 * 当前是否为调试模式
	 * @var boolean
	 */
	private static $isDebug = false;

	/**
	 * 框架服务运行入口
	 * @param string $namespace 应用命名空间
	 * @return void
	 */
	public static function run($namespace)
	{
		static::$namespace = $namespace;
		static::initFramework();
	}

	/**
	 * 框架初始化
	 * @return void
	 */
	private static function initFramework()
	{
		static::$container = new Container;
		// 初始化Main类
		static::initMains();
		// 注解处理
		static::$annotation = Annotation::getInstance();
		static::$annotation->init([
			MainHelper::getMain('Imi', 'Imi'),
		]);
		Event::trigger('IMI.INITED');
		static::$isInited = true;
	}

	/**
	 * 初始化Main类
	 * @return void
	 */
	private static function initMains()
	{
		// 框架
		MainHelper::getMain('Imi', 'Imi');
		// 项目
		MainHelper::getMain(static::$namespace, 'app');
		// 服务器们
		$servers = array_merge(['main'=>Config::get('@app.mainServer')], Config::get('@app.subServers', []));
		foreach($servers as $serverName => $item)
		{
			MainHelper::getMain($item['namespace'], 'server_' . $serverName);
		}
	}

	/**
	 * 创建服务器对象们
	 * @return void
	 */
	public static function createServers()
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

	/**
	 * 当前是否为调试模式
	 * @return boolean
	 */
	public static function isDebug()
	{
		return static::$isDebug;
	}

	/**
	 * 开关调试模式
	 * @param boolean $isDebug
	 * @return void
	 */
	public static function setDebug($isDebug)
	{
		static::$isDebug = $isDebug;
	}

	/**
	 * 框架是否已初始化
	 * @return boolean
	 */
	public static function isInited()
	{
		return static::$isInited;
	}

	/**
	 * 初始化worker
	 *
	 * @return void
	 */
	public static function initWorker()
	{
		$appMains = MainHelper::getAppMains();
		// 加载服务器注解
		Annotation::getInstance()->init($appMains);
		
		// 初始化
		if(Coroutine::isIn())
		{
			foreach($appMains as $main)
			{
				// 协程通道队列初始化
				CoroutineChannelManager::setNames($main->getConfig()['coroutineChannels'] ?? []);
		
				// 异步池子初始化
				$pools = $main->getConfig()['pools'] ?? [];
				foreach($pools as $name => $pool)
				{
					if(isset($pool['async']))
					{
						$pool = $pool['async'];
						PoolManager::addName($name, $pool['pool']['class'], new PoolConfig($pool['pool']['config']), $pool['resource']);
					}
				}
			}
		}
		else
		{
			foreach($appMains as $main)
			{
				// 同步池子初始化
				$pools = $main->getConfig()['pools'] ?? [];
				foreach($pools as $name => $pool)
				{
					if(isset($pool['sync']))
					{
						$pool = $pool['sync'];
						PoolManager::addName($name, $pool['pool']['class'], new PoolConfig($pool['pool']['config']), $pool['resource']);
					}
				}
			}
		}
		// 缓存初始化
		$caches = $main->getConfig()['caches'] ?? [];
		foreach($caches as $name => $cache)
		{
			CacheManager::addName($name, $cache['handlerClass'], $cache['option']);
		}
	}
}