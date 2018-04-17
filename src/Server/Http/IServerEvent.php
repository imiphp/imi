<?php
namespace Imi\Server\Http;

interface IServerEvent
{
	/**
	 * 接收请求
	 * @Listen("request")
	 * @param \swoole_http_request $request 请求
	 * @param \swoole_http_response $response 输出
	 * @return void
	 */
	public function onRequest(\swoole_http_request $request, \swoole_http_response $response);
	
	/**
	 * 关闭连接
	 * @Listen("close")
	 * @param \Server $server
	 * @param integer $fd
	 * @return void
	 */
	public function onClose(\swoole_http_server $server, int $fd);

}