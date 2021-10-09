<?php

declare(strict_types=1);

namespace Imi\Async\Sync;

use Imi\Async\AsyncResult;
use Imi\Async\Contract\IAsyncHandler;
use Imi\Async\Contract\IAsyncResult;

class SyncHandler implements IAsyncHandler
{
    /**
     * 执行.
     */
    public function exec(callable $callable): IAsyncResult
    {
        return new AsyncResult($callable());
    }
}
