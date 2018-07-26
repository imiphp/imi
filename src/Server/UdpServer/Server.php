<?php
namespace Imi\Server\UdpServer;

use Imi\App;
use Imi\Server\Base;
use Imi\ServerManage;
use Imi\Bean\Annotation\Bean;
use Imi\Server\Event\Param\PacketEventParam;

/**
 * UDP 服务器类
 * @Bean
 */
class Server extends Base
{
	/**
	 * 构造方法
	 * @param string $name
	 * @param array $config
	 * @param \swoole_server $serverInstance
	 * @param bool $subServer 是否为子服务器
	 */
	public function __construct($name, $config, $isSubServer = false)
	{
		parent::__construct($name, $config, $isSubServer);
	}

	/**
	 * 创建 swoole 服务器对象
	 * @return void
	 */
	protected function createServer()
	{
		$config = $this->getServerInitConfig();
		$this->swooleServer = new \swoole_server($config['host'], $config['port'], $config['mode'], $config['sockType']);
	}

	/**
	 * 从主服务器监听端口，作为子服务器
	 * @return void
	 */
	protected function createSubServer()
	{
		$config = $this->getServerInitConfig();
		$this->swooleServer = ServerManage::getServer('main')->getSwooleServer();
		$this->swoolePort = $this->swooleServer->addListener($config['host'], $config['port'], $config['sockType']);
	}

	/**
	 * 获取服务器初始化需要的配置
	 * @return array
	 */
	protected function getServerInitConfig()
	{
		return [
			'host'		=>	isset($this->config['host']) ? $this->config['host'] : '0.0.0.0',
			'port'		=>	isset($this->config['port']) ? $this->config['port'] : 8080,
			'sockType'	=>	isset($this->config['sockType']) ? (SWOOLE_SOCK_UDP | $this->config['sockType']) : SWOOLE_SOCK_UDP,
			'mode'		=>	isset($this->config['mode']) ? $this->config['mode'] : SWOOLE_PROCESS,
		];
	}

	/**
	 * 绑定服务器事件
	 * @return void
	 */
	protected function __bindEvents()
	{
		$this->swooleServer->on('packet', function(\swoole_server $server, $data, $clientInfo){
			$this->trigger('packet', [
				'server'	=>	$this,
				'data'		=>	$data,
				'clientInfo'=>	$clientInfo,
			], $this, PacketEventParam::class);
		});
		
	}
}