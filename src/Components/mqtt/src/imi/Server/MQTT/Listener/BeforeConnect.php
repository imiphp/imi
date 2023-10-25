<?php

declare(strict_types=1);

namespace Imi\Server\MQTT\Listener;

use Imi\Bean\Annotation\ClassEventListener;

/**
 * Connect事件前置处理.
 */
#[ClassEventListener(className: \Imi\Server\MQTT\Server::class, eventName: 'connect', priority: 19940312)]
class BeforeConnect extends \Imi\Swoole\Server\TcpServer\Listener\BeforeConnect
{
}
