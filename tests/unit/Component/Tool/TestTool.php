<?php

namespace Imi\Test\Component\Tool;

use Imi\Tool\Annotation\Arg;
use Imi\Tool\Annotation\Operation;
use Imi\Tool\Annotation\Tool;
use Imi\Tool\ArgType;

/**
 * @Tool("TestTool")
 */
class TestTool
{
    /**
     * 测试.
     *
     * @Operation("test")
     * @Arg(name="code", type=ArgType::INT, default=0)
     *
     * @return void
     */
    public function test(int $code)
    {
        exit($code);
    }
}
