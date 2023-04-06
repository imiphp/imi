<?php

declare(strict_types=1);

namespace Imi\Phar
{
    use Composer\XdebugHandler\XdebugHandler;

    class PharHandler extends XdebugHandler
    {
        private $required;

        /**
         * {@inheritDoc}
         */
        protected function requiresRestart($default)
        {
            $this->required = (bool) \ini_get('phar.readonly');

            return $this->required || $default;
        }

        /**
         * {@inheritDoc}
         */
        protected function restart($command)
        {
            if ($this->required)
            {
                # Add required ini setting to tmpIni
                $content = file_get_contents($this->tmpIni);
                $content .= 'phar.readonly=0' . \PHP_EOL;
                file_put_contents($this->tmpIni, $content);
            }

            parent::restart($command);
        }
    }
}

namespace {
}
