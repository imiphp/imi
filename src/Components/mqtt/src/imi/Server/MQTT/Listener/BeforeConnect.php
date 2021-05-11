<?php

namespace Imi\Server\MQTT\Listener;

use Imi\Bean\Annotation\ClassEventListener;

/**
 * Connect事件前置处理.
 *
 * @ClassEventListener(className="Imi\Server\MQTT\Server",eventName="connect",priority=Imi\Util\ImiPriority::IMI_MAX)
 */
class BeforeConnect extends \Imi\Server\TcpServer\Listener\BeforeConnect
{
}
