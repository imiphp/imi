<?php

declare(strict_types=1);

namespace Imi\Test\Component\Async;

if (\PHP_VERSION_ID >= 80000)
{
    eval(<<<'PHP'
    use Imi\Async\AsyncResult;
    use Imi\Async\Contract\IAsyncResult;
    if (!class_exists(AsyncTesterPHP8::class, false))
    {
        /**
         * @Bean("AsyncTesterPHP8")
         */
        class AsyncTesterPHP8
        {
            /**
             * @Async
             */
            public function test1(float $a, float $b): int|IAsyncResult
            {
                return new AsyncResult($a + $b);
            }
        }
    }
    PHP);
}
