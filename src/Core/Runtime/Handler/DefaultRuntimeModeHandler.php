<?php

declare(strict_types=1);

namespace Imi\Core\Runtime\Handler;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Core\CoreEvents;
use Imi\Core\Runtime\Contract\IRuntimeModeHandler;
use Imi\Core\Runtime\Event\BuildRuntimeInfoEvent;
use Imi\Core\Runtime\Event\LoadRuntimeInfoEvent;
use Imi\Event\Event;

#[Bean(name: 'DefaultRuntimeModeHandler')]
class DefaultRuntimeModeHandler implements IRuntimeModeHandler
{
    /**
     * {@inheritDoc}
     */
    public function init(): void
    {
        Event::on(CoreEvents::LOAD_RUNTIME_INFO, static fn (LoadRuntimeInfoEvent $e) => App::newInstance(\Imi\Cli\Listener\LoadRuntimeListener::class)->handle($e), 19940200);
        Event::on(CoreEvents::BUILD_RUNTIME, static fn (BuildRuntimeInfoEvent $e) => App::newInstance(\Imi\Cli\Listener\BuildRuntimeListener::class)->handle($e), 19940200);
    }
}
