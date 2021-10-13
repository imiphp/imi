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
     * {@inheritDoc}
     */
    public function init(): void
    {
    }
}
