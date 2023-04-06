<?php

declare(strict_types=1);

namespace Imi\Phar
{
    use Composer\XdebugHandler\XdebugHandler;

    class PharHandler extends XdebugHandler
    {
        use TPharHandler;
    }
}

namespace {
}
