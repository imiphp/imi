<?php
namespace Imi\Server\Http;

use Imi\Server\Base;
use Imi\Manager;

/**
 * Http 服务器类
 */
class Server extends Base
{
	/**
	 * 事件接口
	 * @var string
	 */
	protected $eventInterface = ServerEvent::class;

	/**
	 * 创建 swoole 服务器对象
	 * @return void
	 */
	protected function createServer()
	{
		$config = $this->getServerInitConfig();
		$this->swooleServer = new \swoole_http_server($config['host'], $config['port'], $config['mode'], $config['sockType']);
	}

	/**
	 * 从主服务器监听端口，作为子服务器
	 * @return void
	 */
	protected function createSubServer()
	{
		$config = $this->getServerInitConfig();
		$this->swooleServer = Manager::getServer('main')->getSwooleServer()->addListener($config['host'], $config['port'], $config['sockType']);
	}

	/**
	 * 获取服务器初始化需要的配置
	 * @return array
	 */
	protected function getServerInitConfig()
	{
		return [
			'host'		=>	isset($this->config['host']) ? $this->config['host'] : '0.0.0.0',
			'port'		=>	isset($this->config['port']) ? $this->config['port'] : 80,
			'sockType'	=>	isset($this->config['sockType']) ? $this->config['sockType'] : SWOOLE_SOCK_TCP,
			'mode'		=>	isset($this->config['mode']) ? $this->config['mode'] : SWOOLE_PROCESS,
		];
	}
}