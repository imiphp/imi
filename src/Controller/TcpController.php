<?php
namespace Imi\Controller;

/**
 * TCP 控制器
 */
abstract class TcpController
{
	/**
	 * 请求
	 * @var \Imi\Server\Tcp\Server
	 */
	public $server;

	/**
	 * 桢
	 * @var \Imi\Server\TcpServer\Message\IReceiveData
	 */
	public $data;
}