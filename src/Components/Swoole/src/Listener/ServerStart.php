<?php

declare(strict_types=1);

namespace Imi\Swoole\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Log\Log;
use Imi\Server\ServerManager;
use Imi\Swoole\Server\Contract\ISwooleServer;
use Imi\Swoole\Server\Event\Listener\IManagerStartEventListener;
use Imi\Swoole\Server\Event\Param\ManagerStartEventParam;
use Imi\Swoole\Util\Imi;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * @Listener(eventName="IMI.MAIN_SERVER.MANAGER.START")
 */
class ServerStart implements IManagerStartEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(ManagerStartEventParam $e): void
    {
        Imi::setProcessName('master');
        Log::info('Server start');
        $output = new ConsoleOutput();
        /** @var ISwooleServer $server */
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
