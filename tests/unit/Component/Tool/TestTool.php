<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tool;

use Imi\Cli\Annotation\Command;
use Imi\Cli\Annotation\CommandAction;
use Imi\Cli\Annotation\Option;
use Imi\Cli\ArgType;

/**
 * @Command("TestTool")
 */
class TestTool
{
    /**
     * 测试.
     *
     * @CommandAction("test")
     * @Option(name="code", type=ArgType::INT, default=0)
     */
    public function test(int $code): void
    {
        var_dump($code);
        exit($code);
    }
}
