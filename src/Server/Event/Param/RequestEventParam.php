<?php
namespace Imi\Server\Event\Param;

use Imi\Event\EventParam;

class RequestEventParam extends EventParam
{
	/**
	 * swoole 请求对象
	 * @var \swoole_http_request
	 */
	public $request;

	/**
	 * swoole 响应对象
	 * @var \swoole_http_response
	 */
	public $response;
}