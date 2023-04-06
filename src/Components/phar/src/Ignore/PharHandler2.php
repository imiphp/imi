<?php

declare(strict_types=1);

namespace Imi\Phar
{
    use Composer\XdebugHandler\XdebugHandler;

    class PharHandler extends XdebugHandler
    {
        use TPharHandler {
            requiresRestart as protected __requiresRestart;
            restart as protected __restart;
        }

        /**
         * {@inheritDoc}
         */
        protected function requiresRestart($default)
        {
            return $this->__requiresRestart($default);
        }

        /**
         * {@inheritDoc}
         */
        protected function restart($command)
        {
            return $this->__restart($command);
        }
    }
}

namespace {
}
