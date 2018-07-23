<?php
namespace Imi\Server\WebSocket\Middleware;

use Imi\Server\WebSocket\Message\IFrame;
use Imi\Server\WebSocket\IMessageHandler;

interface IMiddleware
{
	public function process(IFrame $frame, IMessageHandler $handler);
}