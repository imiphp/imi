<?php

declare(strict_types=1);

namespace Imi\Fpm\Server\Cli;

use Imi\App;
use Imi\AppContexts;
use Imi\Cli\Annotation\Command;
use Imi\Cli\Annotation\CommandAction;
use Imi\Cli\Annotation\Option;
use Imi\Cli\ArgType;
use Imi\Cli\Contract\BaseCommand;
use function Imi\ttyExec;
use Imi\Util\File;

/**
 * @Command("fpm")
 */
class Server extends BaseCommand
{
    /**
     * 启动 php 内置服务器.
     *
     * @CommandAction(name="start", description="启动 php 内置服务器")
     * @Option(name="host", type=ArgType::STRING, default="0.0.0.0", comments="主机名")
     * @Option(name="port", type=ArgType::INT, default=8080, comments="端口")
     */
    public function start(string $host, int $port): void
    {
        if (\function_exists('pcntl_signal'))
        {
            /** @var \Symfony\Component\Process\Process|null $process */
            $process = null;
            pcntl_signal(\SIGTERM, function () use (&$process) {
                if ($process)
                {
                    $process->signal(\SIGINT);
                }
                // 等待 5 秒
                for ($i = 0; $i < 50; ++$i)
                {
                    if (!$process->isRunning())
                    {
                        return;
                    }
                    usleep(100000);
                }
                exit(1);
            });
        }
        exit(ttyExec([
            \PHP_BINARY,
            '-S', $host . ':' . $port,
            '-t', File::path(App::get(AppContexts::APP_PATH), 'public'),
        ], null, $process));
    }
}
