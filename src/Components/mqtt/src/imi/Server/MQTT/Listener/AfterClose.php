<?php

declare(strict_types=1);

namespace Imi\Server\MQTT\Listener;

use Imi\Bean\Annotation\ClassEventListener;

/**
 * Close事件后置处理.
 *
 * @ClassEventListener(className="Imi\Server\MQTT\Server",eventName="close",priority=Imi\Util\ImiPriority::IMI_MIN)
 */
class AfterClose extends \Imi\Swoole\Server\TcpServer\Listener\AfterClose
{
}
