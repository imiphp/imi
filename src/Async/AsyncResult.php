<?php

declare(strict_types=1);

namespace Imi\Async;

use Imi\Async\Contract\IAsyncResult;
use Imi\Log\Log;

class AsyncResult implements IAsyncResult
{
    private bool $isGeted = false;

    public function __construct(private mixed $result = null, private readonly bool $isException = false)
    {
    }

    public function __destruct()
    {
        if (!$this->isGeted && $this->isException)
        {
            Log::error($this->result);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function get(?float $timeout = null): mixed
    {
        $this->isGeted = true;
        $result = $this->result;
        if ($this->isException)
        {
            throw $result;
        }
        if ($result instanceof IAsyncResult)
        {
            return $result->get($timeout);
        }

        return $result;
    }
}
