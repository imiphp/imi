<?php

declare(strict_types=1);

namespace Imi\Server\MQTT\Listener;

use Imi\Bean\Annotation\ClassEventListener;

/**
 * Close事件前置处理.
 */
#[ClassEventListener(className: \Imi\Server\MQTT\Server::class, eventName: 'close', priority: 19940312)]
class BeforeClose extends \Imi\Swoole\Server\TcpServer\Listener\BeforeClose
{
}
