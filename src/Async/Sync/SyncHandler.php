<?php

declare(strict_types=1);

namespace Imi\Async\Sync;

use Imi\Async\AsyncResult;
use Imi\Async\Contract\IAsyncHandler;
use Imi\Async\Contract\IAsyncResult;

class SyncHandler implements IAsyncHandler
{
    /**
     * {@inheritDoc}
     */
    public function exec(callable $callable): IAsyncResult
    {
        try
        {
            return new AsyncResult($callable());
        }
        catch (\Throwable $th)
        {
            return new AsyncResult($th, true);
        }
    }
}
