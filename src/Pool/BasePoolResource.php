<?php

declare(strict_types=1);

namespace Imi\Pool;

use Imi\Pool\Interfaces\IPool;
use Imi\Pool\Interfaces\IPoolResource;
use Imi\Util\Traits\THashCode;

abstract class BasePoolResource implements IPoolResource
{
    use THashCode;

    public function __construct(
        /**
         * 池子实例.
         */
        private readonly ?IPool $pool
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function getPool(): IPool
    {
        return $this->pool;
    }

    /**
     * {@inheritDoc}
     */
    public function isOpened(): bool
    {
        return true;
    }
}
