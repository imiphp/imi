<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Listener;

use Imi\Swoole\Server\Event\Param\HandShakeEventParam;

/**
 * 监听服务器HandShake事件接口.
 */
interface IHandShakeEventListener
{
    public function handle(HandShakeEventParam $e): void;
}
