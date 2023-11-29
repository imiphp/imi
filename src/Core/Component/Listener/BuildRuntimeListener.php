<?php

declare(strict_types=1);

namespace Imi\Core\Component\Listener;

use Imi\Config;
use Imi\Core\Component\ComponentManager;
use Imi\Event\IEventListener;

class BuildRuntimeListener implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        if (!Config::get('@app.imi.runtime.component', true))
        {
            return;
        }
        $eventData = $e->getData();
        $eventData['data']['component'] = [
            'components' => ComponentManager::getComponents(),
        ];
    }
}
