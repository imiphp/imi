<?php
namespace Imi\Server\Http\Route;

use Imi\Server\Route\BaseRouteParam;

class RouteParam extends BaseRouteParam
{
	/**
	 * swoole 请求对象
	 * @var \swoole_http_request
	 */
	public $request;

	public function __construct($request)
	{
		$this->request = $request;
	}
}