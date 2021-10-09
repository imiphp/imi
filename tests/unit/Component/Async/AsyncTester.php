<?php

declare(strict_types=1);

namespace Imi\Test\Component\Async;

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
    public function test1(float $a, float $b): IAsyncResult
    {
        return new AsyncResult($a + $b);
    }

    /**
     * @Async
     *
     * @return float|IAsyncResult
     */
    public function test2(float $a, float $b)
    {
        return $a + $b;
    }
}
