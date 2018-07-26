<?php
namespace Imi\Server\Http;

use Imi\App;
use Imi\Server\Base;
use Imi\ServerManage;
use Imi\Bean\Annotation\Bean;
use Imi\Server\Http\Message\Request;
use Imi\Server\Http\Message\Response;
use Imi\Server\Event\Param\CloseEventParam;
use Imi\Server\Event\Param\RequestEventParam;

/**
 * Http 服务器类
 * @Bean
 */
class Server extends Base
{
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
		$this->swooleServer = ServerManage::getServer('main')->getSwooleServer()->addListener($config['host'], $config['port'], $config['sockType']);
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

	/**
	 * 绑定服务器事件
	 * @return void
	 */
	protected function __bindEvents()
	{
		$this->swooleServer->on('request', function(\swoole_http_request $swooleRequest, \swoole_http_response $swooleResponse){
			$request = new Request($this, $swooleRequest);
			$response = new Response($this, $swooleResponse);
			$this->trigger('request', [
				'request'	=>	&$request,
				'response'	=>	&$response,
			], $this, RequestEventParam::class);
		});

		$this->swooleServer->on('close', function(\swoole_http_server $server, $fd, $reactorID){
			$this->trigger('close', [
				'server'	=>	$this,
				'fd'		=>	$fd,
				'reactorID'	=>	$reactorID,
			], $this, CloseEventParam::class);
		});
	}
}