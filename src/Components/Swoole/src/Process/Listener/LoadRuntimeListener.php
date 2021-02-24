<?php

declare(strict_types=1);

namespace Imi\Swoole\Process\Listener;

use Imi\Config;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Swoole\Process\ProcessManager;
use Imi\Swoole\Process\ProcessPoolManager;
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
        if (!($config['process'] ?? true))
        {
            return;
        }
        $eventData = $e->getData();
        ['fileName' => $fileName] = $eventData;
        $fileName = File::path($fileName, 'swooleProcess.cache');
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
        ProcessManager::setMap($data['process']);
        ProcessPoolManager::setMap($data['processPool']);
    }
}
