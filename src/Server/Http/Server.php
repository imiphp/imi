<?php
namespace Imi\Server\Http;

use Imi\Server\Base;

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
	public function createServer()
	{
		$host = isset($this->config['server']['host']) ? $this->config['server']['host'] : '0.0.0.0';
		$port = isset($this->config['server']['port']) ? $this->config['server']['port'] : 80;
		$mode = isset($this->config['server']['mode']) ? $this->config['server']['mode'] : SWOOLE_PROCESS;
		$sockType = isset($this->config['server']['sockType']) ? $this->config['server']['sockType'] : SWOOLE_SOCK_TCP;
		$this->swooleServer = new \swoole_http_server($host, $port, $mode, $sockType);
	}
}