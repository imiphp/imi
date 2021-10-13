<?php

declare(strict_types=1);

namespace Imi\Swoole\Async;

use Imi\Async\Contract\IAsyncResult;
use Imi\Async\Exception\AsyncTimeoutException;
use Swoole\Coroutine\Channel;

class SwooleResult implements IAsyncResult
{
    private Channel $channel;

    public function __construct(Channel $channel)
    {
        $this->channel = $channel;
    }

    /**
     * {@inheritDoc}
     */
    public function get(?float $timeout = null)
    {
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

        return $result['result'];
    }
}
