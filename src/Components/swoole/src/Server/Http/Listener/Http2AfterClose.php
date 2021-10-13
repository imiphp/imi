<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Http\Listener;

use Imi\Server\ConnectionContext\Traits\TConnectionContextRelease;
use Imi\Swoole\Server\Event\Listener\ICloseEventListener;
use Imi\Swoole\Server\Event\Param\CloseEventParam;

class Http2AfterClose implements ICloseEventListener
{
    use TConnectionContextRelease;

    /**
     * {@inheritDoc}
     */
    public function handle(CloseEventParam $e): void
    {
        $this->release($e->clientId);
    }
}
