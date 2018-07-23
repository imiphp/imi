<?php
namespace Imi\Server\Route\Annotation\WebSocket;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * WebSocket 控制器注解
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Server\Route\Parser\WSControllerParser")
 */
class WSController extends Base
{
	/**
	 * 只传一个参数时的参数名
	 * @var string
	 */
	protected $defaultFieldName = 'prefix';

}