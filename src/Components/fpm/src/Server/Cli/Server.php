<?php

declare(strict_types=1);

namespace Imi\Fpm\Server\Cli;

use Imi\App;
use Imi\Cli\Annotation\Command;
use Imi\Cli\Annotation\CommandAction;
use Imi\Cli\Annotation\Option;
use Imi\Cli\ArgType;
use Imi\Cli\Contract\BaseCommand;
use Imi\Util\File;
use Imi\Util\Imi;

/**
 * @Command("fpm")
 */
class Server extends BaseCommand
{
    /**
     * 启动 php 内置服务器.
     *
     * @CommandAction(name="start")
     * @Option(name="host", type=ArgType::STRING, default="0.0.0.0", comments="主机名")
     * @Option(name="port", type=ArgType::INT, default=8080, comments="端口")
     */
    public function start(string $host, int $port): void
    {
        $cmd = '"' . \PHP_BINARY . '" -S ' . $host . ':' . $port . ' "' . File::path(Imi::getNamespacePath(App::getNamespace()), 'public', 'index.php') . '"';
        $descriptorspec = [
            ['pipe', 'r'],  // 标准输入，子进程从此管道中读取数据
            ['pipe', 'w'],  // 标准输出，子进程向此管道中写入数据
        ];
        $p = proc_open(\Imi\cmd($cmd), $descriptorspec, $pipes, null, null, [
            'bypass_shell' => true,
        ]);
        while ($tmp = fgets($pipes[1]))
        {
            echo $tmp;
        }
        proc_close($p);
    }
}
