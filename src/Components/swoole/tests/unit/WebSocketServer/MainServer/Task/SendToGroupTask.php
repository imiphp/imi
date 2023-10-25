<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\WebSocketServer\MainServer\Task;

use Imi\Server\Server;
use Imi\Server\ServerManager;
use Imi\Swoole\Task\Annotation\Task;
use Imi\Swoole\Task\Interfaces\ITaskHandler;
use Imi\Swoole\Task\TaskParam;

#[Task(name: 'SendToGroupTask')]
class SendToGroupTask implements ITaskHandler
{
    /**
     * 任务处理方法.
     *
     * @return mixed
     */
    public function handle(TaskParam $param, \Swoole\Server $server, int $taskId, int $workerId)
    {
        $group = $param->getData()['group'];
        $data = [
            'data'  => 'test',
        ];
        $dataStr = json_encode($data);
        $result = [
            // @phpstan-ignore-next-line
            'groupClientIdCount' => ServerManager::getServer('main')->getGroup($group)->count(),
        ];

        $result['sendToGroup'] = Server::sendToGroup($group, $data, 'main');
        $result['sendRawToGroup'] = Server::sendRawToGroup($group, $dataStr, 'main');

        return $result;
    }

    /**
     * 任务结束时触发.
     *
     * @param mixed $data
     */
    public function finish(\Swoole\Server $server, int $taskId, $data): void
    {
    }
}
