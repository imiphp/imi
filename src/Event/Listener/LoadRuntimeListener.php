<?php

declare(strict_types=1);

namespace Imi\Event\Listener;

use Imi\Config;
use Imi\Event\ClassEventManager;
use Imi\Event\EventManager;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Util\File;

class LoadRuntimeListener implements IEventListener
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
        $config = Config::get('@app.imi.runtime', []);
        if (!($config['event'] ?? true))
        {
            return;
        }
        $eventData = $e->getData();
        ['fileName' => $fileName] = $eventData;
        $fileName = File::path($fileName, 'event.cache');
        if (!$fileName || !is_file($fileName))
        {
            $eventData['success'] = false;
            $e->stopPropagation();

            return;
        }
        $data = unserialize(file_get_contents($fileName));
        if (!$data)
        {
            $eventData['success'] = false;
            $e->stopPropagation();

            return;
        }
        EventManager::setMap($data['event']);
        ClassEventManager::setMap($data['classEvent']);
    }
}
