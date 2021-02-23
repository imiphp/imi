<?php

declare(strict_types=1);

namespace Imi\Enum\Listener;

use Imi\Config;
use Imi\Enum\EnumManager;
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
        if (!($config['enum'] ?? true))
        {
            return;
        }
        $eventData = $e->getData();
        ['fileName' => $fileName] = $eventData;
        $fileName = File::path($fileName, 'enum.cache');
        if (!$fileName || !is_file($fileName))
        {
            $eventData['success'] = false;

            return;
        }
        $data = unserialize(file_get_contents($fileName));
        if (!$data)
        {
            $eventData['success'] = false;

            return;
        }
        EnumManager::setMap($data['enum']);
    }
}
