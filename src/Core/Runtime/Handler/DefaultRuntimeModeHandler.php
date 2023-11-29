<?php

declare(strict_types=1);

namespace Imi\Core\Runtime\Handler;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Core\Runtime\Contract\IRuntimeModeHandler;
use Imi\Event\Contract\IEvent;
use Imi\Event\Event;

#[Bean(name: 'DefaultRuntimeModeHandler')]
class DefaultRuntimeModeHandler implements IRuntimeModeHandler
{
    /**
     * {@inheritDoc}
     */
    public function init(): void
    {
        Event::on('IMI.LOAD_RUNTIME_INFO', static fn (IEvent $e) => App::newInstance(\Imi\Cli\Listener\LoadRuntimeListener::class)->handle($e), 19940200);
        Event::on('IMI.BUILD_RUNTIME', static fn (IEvent $e) => App::newInstance(\Imi\Cli\Listener\BuildRuntimeListener::class)->handle($e), 19940200);
    }
}
