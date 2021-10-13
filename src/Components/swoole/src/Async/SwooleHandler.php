<?php

declare(strict_types=1);

namespace Imi\Swoole\Async;

use Imi\Async\Contract\IAsyncHandler;
use Imi\Async\Contract\IAsyncResult;
use Swoole\Coroutine\Channel;

class SwooleHandler implements IAsyncHandler
{
    /**
     * {@inheritDoc}
     */
    public function exec(callable $callable): IAsyncResult
    {
        $channel = new Channel();
        $result = new SwooleResult($channel);
        imigo(function () use ($callable, $channel) {
            try
            {
                $channel->push([
                    'result'    => $callable(),
                    'exception' => false,
                ]);
            }
            catch (\Throwable $th)
            {
                $channel->push([
                    'result'    => $th,
                    'exception' => true,
                ]);
            }
        });

        return $result;
    }
}
