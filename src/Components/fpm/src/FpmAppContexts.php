<?php

declare(strict_types=1);

namespace Imi\Fpm;

class FpmAppContexts
{
    use \Imi\Util\Traits\TStaticClass;

    /**
     * 进程类型.
     */
    public const ROUTE_INITED = 'fpm_route_inited';
}
