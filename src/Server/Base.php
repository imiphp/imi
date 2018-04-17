<?php
namespace Imi\Server;

use Imi\Server\Annotation\Listen;
use Doctrine\Common\Annotations\AnnotationReader;

abstract class Base
{
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
	 * 事件接口
	 * @var string
	 */
	protected $eventInterface;

	/**
	 * 事件对象
	 * @var \Imi\Server\Http\IServerEvent
	 */
	protected $eventInstance;

	public function __construct($config)
	{
		$this->config = $config;
		$this->createServer();
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
	 * 绑定服务器事件
	 * @return void
	 */
	public function bindEvents()
	{
		$reader = new AnnotationReader();
		$ref = new \ReflectionClass($this->eventInterface);
		$this->eventInstance = $ref->newInstance();
		foreach($ref->getMethods(\ReflectionMethod::IS_PUBLIC) as $method)
		{
			$annotations = $reader->getMethodAnnotations($method);
			foreach($annotations as $item)
			{
				if($item instanceof Listen)
				{
					$this->swooleServer->on($item->name, [$this->eventInstance, $method->name]);
				}
			}
		}
	}

	public function start()
	{
		$this->swooleServer->start();
	}

	/**
	 * 创建 swoole 服务器对象
	 * @return void
	 */
	public abstract function createServer();

}