<?php

declare(strict_types=1);

namespace Imi\Core\Runtime\Handler;

use Imi\Bean\Annotation\Bean;
use Imi\Core\Runtime\Contract\IRuntimeModeHandler;
use Imi\Event\Event;

/**
 * @Bean("DefaultRuntimeModeHandler")
 */
class DefaultRuntimeModeHandler implements IRuntimeModeHandler
{
    /**
     * {@inheritDoc}
     */
    public function init(): void
    {
        Event::on('IMI.LOAD_RUNTIME_INFO', \Imi\Cli\Listener\LoadRuntimeListener::class, 19940200);
        Event::on('IMI.BUILD_RUNTIME', \Imi\Cli\Listener\BuildRuntimeListener::class, 19940200);
    }
}
