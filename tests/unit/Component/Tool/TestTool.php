<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tool;

use Imi\Cli\Annotation\Argument;
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
     * @Option(name="a1", shortcut="a", type=ArgType::BOOL, default=true, to="x")
     * @Option(name="b2", shortcut="b", type=ArgType::BOOL, default=false, to="y")
     */
    public function testBool(bool $a1, bool $b2, bool $x, bool $y): void
    {
        var_dump($a1, $x);
        var_dump($this->input->getOption('a1'));
        var_dump($b2, $y);
        var_dump($this->input->getOption('b2'));
    }

    /**
     * @CommandAction(name="testArgument", dynamicOptions=true)
     *
     * @Argument(name="content", type=ArgType::STRING, default="", to="content2")
     */
    public function testArgument(string $content, string $content2): void
    {
        var_dump($content, $content2);
        var_dump($this->input->getArgument('content'));
    }
}
