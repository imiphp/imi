<?php

declare(strict_types=1);

namespace Imi\Swoole\Task\Listener;

use Imi\Config;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Swoole\Task\TaskManager;
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
        $config = Config::get('@app.imi.runtime.swoole', []);
        if (!($config['task'] ?? true))
        {
            return;
        }
        $eventData = $e->getData();
        ['fileName' => $fileName] = $eventData;
        $fileName = File::path($fileName, 'swooleTask.cache');
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
        TaskManager::setMap($data['task']);
    }
}
