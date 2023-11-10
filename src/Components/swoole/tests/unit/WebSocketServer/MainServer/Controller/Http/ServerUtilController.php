<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\WebSocketServer\MainServer\Controller\Http;

use Imi\ConnectionContext;
use Imi\Server\Http\Controller\HttpController;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Server\ServerManager;
use Imi\Swoole\Server\Server;
use Imi\Swoole\Task\TaskManager;
use Imi\Worker;

/**
 * 服务器工具类.
 */
#[Controller(prefix: '/serverUtil/')]
class ServerUtilController extends HttpController
{
    #[Action]
    public function info(): array
    {
        return [
            'clientId' => ConnectionContext::getClientId(),
            'workerId' => Worker::getWorkerId(),
        ];
    }

    #[Action]
    public function getServer(): array
    {
        $result = [];
        $server = Server::getServer();
        $result['null'] = $server->getName();
        $server = Server::getServer('main');
        $result['main'] = $server->getName();
        $server = Server::getServer('not found');
        $result['notFound'] = null === $server;

        return $result;
    }

    #[Action]
    public function sendMessage(): array
    {
        $result = [];
        $result['sendMessageAll'] = Server::sendMessage('test');
        $result['sendMessage1'] = Server::sendMessage('test', [], 0);
        $result['sendMessage2'] = Server::sendMessage('test', [], [0, 1]);
        $result['sendMessageRawAll'] = Server::sendMessageRaw('test');
        $result['sendMessageRaw1'] = Server::sendMessageRaw('test', 0);
        $result['sendMessageRaw2'] = Server::sendMessageRaw('test', [0, 1]);

        return $result;
    }

    #[Action]
    public function send(array $clientIds, string $flag): array
    {
        $data = [
            'data'  => 'test',
        ];
        $dataStr = json_encode($data);
        $result = [];
        $result['send1'] = Server::send($data);
        $result['send2'] = Server::send($data, $clientIds[0]);
        $result['send3'] = Server::send($data, $clientIds);
        $result['sendByFlag'] = Server::sendByFlag($data, $flag);
        $result['sendRaw1'] = Server::sendRaw($dataStr);
        $result['sendRaw2'] = Server::sendRaw($dataStr, $clientIds[0]);
        $result['sendRaw3'] = Server::sendRaw($dataStr, $clientIds);
        $result['sendRawByFlag'] = Server::sendRawByFlag($dataStr, $flag);

        $result['sendToAll'] = Server::sendToAll($data);
        $result['sendRawToAll'] = Server::sendRawToAll($dataStr);

        return $result;
    }

    #[Action]
    public function sendToGroup(string $group): array
    {
        $data = [
            'data'  => 'test',
        ];
        $dataStr = json_encode($data);
        $result = [
            // @phpstan-ignore-next-line
            'groupClientIdCount' => ServerManager::getServer('main')->getGroup($group)->count(),
        ];

        $result['sendToGroup'] = Server::sendToGroup($group, $data);
        $result['sendRawToGroup'] = Server::sendRawToGroup($group, $dataStr);

        return $result;
    }

    #[Action]
    public function sendToGroupTask(string $group): array
    {
        return TaskManager::nPostWait('SendToGroupTask', [
            'group' => $group,
        ], 3);
    }

    #[Action]
    public function exists(int|string $clientId, string $flag): array
    {
        return [
            'clientId'   => Server::exists($clientId),
            'flag'       => Server::flagExists($flag),
        ];
    }

    #[Action]
    public function close(int|string $clientId, string $flag): array
    {
        return [
            'clientId'   => Server::close($clientId),
            'flag'       => Server::closeByFlag($flag),
        ];
    }

    #[Action]
    public function getConnectionCount(): array
    {
        return [
            'count' => Server::getConnectionCount(),
        ];
    }
}
