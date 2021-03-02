<?php

declare(strict_types=1);

namespace Imi\Workerman\Process\Cli;

use Imi\Bean\Scanner;
use Imi\Cli\Annotation\Argument;
use Imi\Cli\Annotation\Command;
use Imi\Cli\Annotation\CommandAction;
use Imi\Cli\ArgType;
use Imi\Cli\Contract\BaseCommand;
use Imi\Workerman\Process\ProcessManager;

/**
 * @Command("process")
 */
class Process extends BaseCommand
{
    /**
     * 开启一个进程，可以任意添加参数.
     *
     * @CommandAction(name="start", dynamicOptions=true)
     *
     * @Argument(name="name", type=ArgType::STRING, required=true, comments="进程名称，通过@Process注解定义")
     *
     * @return void
     */
    public function start(string $name): void
    {
        // 加载服务器注解
        Scanner::scanVendor();
        Scanner::scanApp();
        $worker = ProcessManager::newProcess($name);
        ($worker->onWorkerStart)();
    }
}
