<?php

declare(strict_types=1);

namespace Imi\Swoole\Async;

use Imi\Async\Contract\IAsyncResult;
use Imi\Async\Exception\AsyncTimeoutException;
use Imi\Log\Log;
use Swoole\Coroutine\Channel;

class SwooleResult implements IAsyncResult
{
    private bool $isGeted = false;

    public function __construct(private ?Channel $channel)
    {
    }

    public function __destruct()
    {
        if (!$this->isGeted)
        {
            $channel = $this->channel;
            if (!$channel->isEmpty())
            {
                $result = $channel->pop();
                if (false !== $result && $result['exception'])
                {
                    Log::error($result['result']);
                }
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function get(?float $timeout = null): mixed
    {
        $this->isGeted = true;
        $channel = $this->channel;
        $result = $channel->pop($timeout ?? -1);
        if (false === $result && \SWOOLE_CHANNEL_TIMEOUT === $channel->errCode)
        {
            throw new AsyncTimeoutException();
        }
        if ($result['exception'])
        {
            throw $result['result'];
        }

        $result = $result['result'];
        if ($result instanceof IAsyncResult)
        {
            return $result->get($timeout);
        }

        return $result;
    }
}
