<?php

declare(strict_types=1);

namespace Imi\Enum\Listener;

use Imi\Config;
use Imi\Enum\EnumManager;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;

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
        $data = $e->getData()['data']['enum'] ?? [];
        EnumManager::setMap($data['enum']);
    }
}
