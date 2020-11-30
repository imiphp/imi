<?php

declare(strict_types=1);

namespace Imi\Server\Http\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Util\Imi;

/**
 * @Listener(eventName="IMI.CO_SERVER.START",priority=Imi\Util\ImiPriority::IMI_MAX)
 */
class OnCoServerStart implements IEventListener
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
        $object = $e->getTarget();
        // 进程PID记录
        $fileName = Imi::getRuntimePath(str_replace('\\', '-', App::getNamespace()) . '.pid');
        file_put_contents($fileName, json_encode([
            'masterPID'     => $object->getPID(),
            'managerPID'    => null,
        ]));
    }
}
