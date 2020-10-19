<?php

namespace Imi\Test\HttpServer\Modules\Test\Service;

use Imi\Bean\Annotation\Bean;

/**
 * @Bean("TestService")
 */
class TestService
{
    /**
     * 测试方法.
     *
     * @param string $time
     *
     * @return string
     */
    public function test($time)
    {
        return 'now: ' . date('Y-m-d H:i:s', $time);
    }
}
