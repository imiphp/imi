<?php

declare(strict_types=1);

namespace Imi\Fpm\Runtime\Handler;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Config;
use Imi\Core\Runtime\Contract\IRuntimeModeHandler;

/**
 * @Bean("FpmRuntimeModeHandler")
 */
class FpmRuntimeModeHandler implements IRuntimeModeHandler
{
    /**
     * 初始化.
     *
     * @return void
     */
    public function init()
    {
        if (!App::isDebug())
        {
            foreach ([
                '@app.imi.runtime.annotation_parser_data',
                '@app.imi.runtime.annotation_parser_parsers',
                '@app.imi.runtime.annotation_manager_annotations',
                '@app.imi.runtime.annotation_manager_annotation_relation',
            ] as $name)
            {
                if (null === Config::get($name))
                {
                    Config::set($name, false);
                }
            }
        }
    }
}
