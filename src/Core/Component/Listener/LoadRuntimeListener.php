<?php

declare(strict_types=1);

namespace Imi\Core\Component\Listener;

use Imi\Config;
use Imi\Core\Component\ComponentManager;
use Imi\Event\IEventListener;

class LoadRuntimeListener implements IEventListener
{
    /**
     * @param \Imi\Core\Runtime\Event\LoadRuntimeInfoEvent $e
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        $config = Config::get('@app.imi.runtime', []);
        if (!($config['component'] ?? true))
        {
            return;
        }
        ComponentManager::setComponents($e->data['component']['components'] ?? []);
    }
}
