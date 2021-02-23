<?php

declare(strict_types=1);

namespace Imi\Swoole\Task\Listener;

use Imi\Config;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Swoole\Task\TaskManager;
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
        if (!Config::get('@app.imi.runtime.swoole.task', true))
        {
            return;
        }
        ['fileName' => $fileName] = $e->getData();
        $fileName = File::path($fileName, 'swooleTask.cache');
        $data = [];
        $data['task'] = TaskManager::getMap();

        file_put_contents($fileName, serialize($data));
    }
}
