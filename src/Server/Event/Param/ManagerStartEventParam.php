<?php
namespace Imi\Server\Event\Param;

use Imi\Event\EventParam;

class ManagerStartEventParam extends EventParam
{
	/**
	 * swoole 服务器对象
	 * @var \swoole_server
	 */
	public $server;

}