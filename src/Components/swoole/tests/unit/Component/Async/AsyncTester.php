<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\Component\Async;

use Imi\Async\Annotation\Async;
use Imi\Async\Annotation\Defer;
use Imi\Async\Annotation\DeferAsync;
use Imi\Async\AsyncResult;
use Imi\Async\Contract\IAsyncResult;
use Imi\Bean\Annotation\Bean;

#[Bean(name: 'AsyncTester')]
class AsyncTester
{
    #[Async]
    public function testAsync1(): void
    {
        sleep(1);
    }

    #[Async]
    public function testAsync2(float $a, float $b): IAsyncResult
    {
        return new AsyncResult($a + $b);
    }

    #[Async]
    public function testAsync3(float $a, float $b): float|IAsyncResult
    {
        return $a + $b;
    }

    #[Async]
    public function testAsync4(): IAsyncResult
    {
        sleep(1);

        return new AsyncResult(true);
    }

    #[Defer]
    public function testDefer1(): void
    {
        sleep(1);
    }

    #[Defer]
    public function testDefer2(float $a, float $b): IAsyncResult
    {
        return new AsyncResult($a + $b);
    }

    #[Defer]
    public function testDefer3(float $a, float $b): float|IAsyncResult
    {
        return $a + $b;
    }

    #[DeferAsync]
    public function testDeferAsync1(): void
    {
        sleep(1);
    }

    #[Defer]
    public function testDeferAsync2(float $a, float $b): IAsyncResult
    {
        return new AsyncResult($a + $b);
    }

    #[Defer]
    public function testDeferAsync3(float $a, float $b): float|IAsyncResult
    {
        return $a + $b;
    }

    #[Async]
    public function testException(): IAsyncResult
    {
        throw new \RuntimeException('gg');
    }
}
