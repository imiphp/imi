<?php

declare(strict_types=1);

namespace Imi\Async;

use Imi\Async\Contract\IAsyncResult;
use Imi\Log\Log;

class AsyncResult implements IAsyncResult
{
    private bool $isGeted = false;

    /**
     * @param mixed $result
     */
    public function __construct(private $result = null, private readonly bool $isException = false)
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
    public function get(?float $timeout = null)
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
