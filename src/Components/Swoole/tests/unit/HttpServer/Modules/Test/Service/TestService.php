<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Modules\Test\Service;

use Imi\Bean\Annotation\Bean;

/**
 * @Bean("TestService")
 */
class TestService
{
    /**
     * 测试方法.
     */
    public function test(int $time): string
    {
        return 'now: ' . date('Y-m-d H:i:s', $time);
    }
}
