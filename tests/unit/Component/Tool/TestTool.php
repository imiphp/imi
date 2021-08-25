<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tool;

use Imi\Cli\Annotation\Command;
use Imi\Cli\Annotation\CommandAction;
use Imi\Cli\Annotation\Option;
use Imi\Cli\ArgType;
use Imi\Cli\Contract\BaseCommand;

/**
 * @Command("TestTool")
 */
class TestTool extends BaseCommand
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

    /**
     * @CommandAction(name="testBool", dynamicOptions=true)
     *
     * @Option(name="a1", shortcut="a", type=ArgType::BOOL, default=true)
     * @Option(name="b2", shortcut="b", type=ArgType::BOOL, default=false)
     *
     * @return void
     */
    public function testBool()
    {
        var_dump($this->input->getOption('a1'));
        var_dump($this->input->getOption('b2'));
    }
}
