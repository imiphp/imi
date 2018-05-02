<?php
namespace Imi\Server;

use Imi\Event\TEvent;
use Imi\Server\Annotation\Listen;
use Doctrine\Common\Annotations\AnnotationReader;

abstract class Base
{
	use TEvent;
	
	/**
	 * swoole 服务器对象
	 * @var \swoole_server
	 */
	protected $swooleServer;

	/**
	 * 服务器配置
	 * @var array
	 */
	protected $config;

	/**
	 * 是否为子服务器
	 * @var bool
	 */
	protected $isSubServer;

	/**
	 * 构造方法
	 * @param array $config
	 * @param \swoole_server $serverInstance
	 * @param bool $subServer 是否为子服务器
	 */
	public function __construct($config, $isSubServer = false)
	{
		$this->config = $config;
		$this->isSubServer = $isSubServer;
		if($isSubServer)
		{
			$this->createSubServer();
		}
		else
		{
			$this->createServer();
		}
		if(!empty($config['configs']))
		{
			$this->swooleServer->set($config['configs']);
		}
		$this->bindEvents();
	}

	/**
	 * 获取 swoole 服务器对象
	 * @return \swoole_server
	 */
	public function getSwooleServer()
	{
		return $this->swooleServer;
	}

	/**
	 * 获取服务器事件处理对象实例
	 * @return object
	 */
	public function getEventInstance()
	{
		return $this->eventInstance;
	}

	/**
	 * 是否为子服务器
	 * @return boolean
	 */
	public function isSubServer()
	{
		return $this->isSubServer;
	}

	/**
	 * 绑定服务器事件
	 * @return void
	 */
	protected abstract function bindEvents();

	/**
	 * 创建 swoole 服务器对象
	 * @return void
	 */
	protected abstract function createServer();

	/**
	 * 从主服务器监听端口，作为子服务器
	 * @return void
	 */
	protected abstract function createSubServer();

	/**
	 * 获取服务器初始化需要的配置
	 * @return array
	 */
	protected abstract function getServerInitConfig();
}