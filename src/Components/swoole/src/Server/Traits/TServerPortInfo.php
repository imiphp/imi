<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Traits;

use Imi\Cli\ImiCommand;
use Imi\Server\ServerManager;
use Imi\Swoole\Server\Contract\ISwooleServer;

trait TServerPortInfo
{
    public function outputServerInfo(): void
    {
        $output = ImiCommand::getOutput();
        $server = ServerManager::getServer('main', ISwooleServer::class);
        $mainSwooleServer = $server->getSwooleServer();
        $output->writeln('<info>WorkerNum: </info>' . $mainSwooleServer->setting['worker_num'] . ', <info>TaskWorkerNum: </info>' . $mainSwooleServer->setting['task_worker_num']);
        foreach (ServerManager::getServers(ISwooleServer::class) as $server)
        {
            /** @var ISwooleServer $server */
            $serverPort = $server->getSwoolePort();
            $output->writeln('<info>[' . $server->getConfig()['type'] . ']</info> <comment>' . $server->getName() . '</comment>; <info>listen:</info> ' . $serverPort->host . ':' . $serverPort->port);
        }
    }
}
