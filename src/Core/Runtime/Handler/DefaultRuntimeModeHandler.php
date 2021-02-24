<?php

declare(strict_types=1);

namespace Imi\Core\Runtime\Handler;

use Imi\Bean\Annotation\Bean;
use Imi\Core\Runtime\Contract\IRuntimeModeHandler;

/**
 * @Bean("DefaultRuntimeModeHandler")
 */
class DefaultRuntimeModeHandler implements IRuntimeModeHandler
{
    /**
     * 初始化.
     *
     * @return void
     */
    public function init()
    {
    }
}
