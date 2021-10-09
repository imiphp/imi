<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\Component\Async;

use Imi\Async\Annotation\Async;
use Imi\Async\AsyncResult;
use Imi\Async\Contract\IAsyncResult;
use Imi\Bean\Annotation\Bean;

/**
 * @Bean("AsyncTester")
 */
class AsyncTester
{
    /**
     * @Async
     */
    public function test1(): void
    {
        sleep(1);
    }

    /**
     * @Async
     */
    public function test2(float $a, float $b): IAsyncResult
    {
        return new AsyncResult($a + $b);
    }

    /**
     * @Async
     *
     * @return float|IAsyncResult
     */
    public function test3(float $a, float $b)
    {
        return $a + $b;
    }

    /**
     * @Async
     */
    public function test4(): IAsyncResult
    {
        sleep(1);

        return new AsyncResult(true);
    }

    /**
     * @Async
     */
    public function testException(): IAsyncResult
    {
        throw new \RuntimeException('gg');
    }
}
