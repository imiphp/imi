<?php

declare(strict_types=1);

namespace Imi\Event\Listener;

use Imi\Config;
use Imi\Event\ClassEventManager;
use Imi\Event\EventManager;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Util\File;

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
        if (!Config::get('@app.imi.runtime.event', true))
        {
            return;
        }
        ['fileName' => $fileName] = $e->getData();
        $fileName = File::path($fileName, 'event.cache');
        $data = [];
        $data['event'] = EventManager::getMap();
        $data['classEvent'] = ClassEventManager::getMap();

        file_put_contents($fileName, serialize($data));
    }
}
