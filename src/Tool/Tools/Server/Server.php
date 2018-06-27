<?php
namespace Imi\Tool\Tools\Server;

use Imi\App;
use Imi\Util\Args;
use Imi\ServerManage;
use Imi\Tool\ArgType;
use Imi\Tool\Annotation\Arg;
use Imi\Tool\Annotation\Tool;
use Imi\Tool\Annotation\Operation;

/**
 * @Tool("server")
 */
class Server
{
	/**
	 * 开启服务
	 * 
	 * @Operation("start")
	 * 
	 * @return void
	 */
	public function start()
	{
		App::createServers();
		ServerManage::getServer('main')->getSwooleServer()->start();
	}
}