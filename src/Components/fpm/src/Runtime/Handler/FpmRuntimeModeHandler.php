<?php

declare(strict_types=1);

namespace Imi\Fpm\Runtime\Handler;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Config;
use Imi\Core\Runtime\Contract\IRuntimeModeHandler;
use Imi\Event\Event;

/**
 * @Bean("FpmRuntimeModeHandler")
 */
class FpmRuntimeModeHandler implements IRuntimeModeHandler
{
    /**
     * {@inheritDoc}
     */
    public function init(): void
    {
        if (!App::isDebug())
        {
            foreach ([
                '@app.imi.runtime.annotation_manager_annotations',
                '@app.imi.runtime.annotation_manager_annotations_cache',
                '@app.imi.runtime.annotation_manager_annotation_relation',
                '@app.imi.runtime.annotation_manager_annotation_relation_cache',
            ] as $name)
            {
                if (null === Config::get($name))
                {
                    Config::set($name, false);
                }
            }
        }
        Event::on('IMI.BUILD_RUNTIME', \Imi\Fpm\Server\Http\Listener\BuildRuntimeListener::class);
        Event::on('IMI.LOAD_RUNTIME_INFO', \Imi\Fpm\Server\Http\Listener\LoadRuntimeListener::class);
    }
}
