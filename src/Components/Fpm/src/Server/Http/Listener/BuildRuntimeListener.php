<?php

declare(strict_types=1);

namespace Imi\Fpm\Server\Http\Listener;

use Imi\Config;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;

class BuildRuntimeListener implements IEventListener
{
    /**
     * 事件处理方法.
     *
     * @param EventParam $e
     *
     * @return void
     */
    public function handle(EventParam $e)
    {
        if (!Config::get('@app.imi.runtime.route', true))
        {
            return;
        }
    }
}
