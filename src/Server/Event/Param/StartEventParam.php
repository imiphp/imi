<?php
namespace Imi\Server\Event\Param;

use Imi\Event\EventParam;

class StartEventParam extends EventParam
{
	/**
	 * swoole 服务器对象
	 * @var \swoole_server
	 */
	public $server;

}