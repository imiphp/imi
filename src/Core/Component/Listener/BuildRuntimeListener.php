<?php

declare(strict_types=1);

namespace Imi\Core\Component\Listener;

use Imi\Config;
use Imi\Core\Component\ComponentManager;
use Imi\Event\IEventListener;

class BuildRuntimeListener implements IEventListener
{
    /**
     * @param \Imi\Core\Runtime\Event\BuildRuntimeInfoEvent $e
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        if (!Config::get('@app.imi.runtime.component', true))
        {
            return;
        }
        $e->data['component'] = [
            'components' => ComponentManager::getComponents(),
        ];
    }
}
