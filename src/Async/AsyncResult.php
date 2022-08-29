<?php

declare(strict_types=1);

namespace Imi\Async;

use Imi\Async\Contract\IAsyncResult;
use Imi\Log\Log;

class AsyncResult implements IAsyncResult
{
    /**
     * @var mixed
     */
    private $result;

    private bool $isException = false;

    private bool $isGeted = false;

    /**
     * @param mixed $result
     */
    public function __construct($result = null, bool $isException = false)
    {
        $this->result = $result;
        $this->isException = $isException;
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
