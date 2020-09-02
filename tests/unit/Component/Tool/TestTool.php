<?php
namespace Imi\Test\Component\Tool;

use Imi\Cli\ArgType;
use Imi\Cli\Annotation\Option;
use Imi\Cli\Annotation\Command;
use Imi\Cli\Annotation\CommandAction;

/**
 * @Command("TestTool")
 */
class TestTool
{
    /**
     * 测试
     * 
     * @CommandAction("test")
     * @Option(name="code", type=ArgType::INT, default=0)
     * 
     * @return void
     */
    public function test(int $code)
    {
        exit($code);
    }

}
