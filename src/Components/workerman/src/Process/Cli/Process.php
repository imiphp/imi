<?php

declare(strict_types=1);

namespace Imi\Workerman\Process\Cli;

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
     * @CommandAction(name="start", dynamicOptions=true, description="启动一个进程")
     *
     * @Argument(name="name", type=ArgType::STRING, required=true, comments="进程名称，通过@Process注解定义")
     */
    public function start(string $name): void
    {
        $worker = ProcessManager::newProcess($name);
        ($worker->onWorkerStart)();
    }
}
