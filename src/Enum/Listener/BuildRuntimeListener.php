<?php

declare(strict_types=1);

namespace Imi\Enum\Listener;

use Imi\Config;
use Imi\Enum\EnumManager;
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
        if (!Config::get('@app.imi.runtime.enum', true))
        {
            return;
        }
        ['fileName' => $fileName] = $e->getData();
        $fileName = File::path($fileName, 'enum.cache');
        $data = [];
        $data['enum'] = EnumManager::getMap();

        file_put_contents($fileName, serialize($data));
    }
}
