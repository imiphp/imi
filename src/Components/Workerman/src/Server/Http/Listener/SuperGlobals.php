<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Http\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;

/**
 * @Listener(eventName="IMI.WORKERMAN.SERVER.WORKER_START")
 */
class SuperGlobals implements IEventListener
{
    public function handle(EventParam $e)
    {
        /** @var \Imi\Server\Http\SuperGlobals\Listener\SuperGlobals $superGlobals */
        $superGlobals = App::getBean('SuperGlobals');
        $superGlobals->bind();
    }
}
