<?php

declare(strict_types=1);

namespace Imi\Swoole\Process\Listener;

use Imi\Config;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Swoole\Process\ProcessManager;
use Imi\Swoole\Process\ProcessPoolManager;
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
        if (!Config::get('@app.imi.runtime.swoole.process', true))
        {
            return;
        }
        ['fileName' => $fileName] = $e->getData();
        $fileName = File::path($fileName, 'swooleProcess.cache');
        $data = [];
        $data['process'] = ProcessManager::getMap();
        $data['processPool'] = ProcessPoolManager::getMap();

        file_put_contents($fileName, serialize($data));
    }
}
