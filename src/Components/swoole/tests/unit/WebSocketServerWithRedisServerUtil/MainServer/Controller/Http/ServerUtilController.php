<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\WebSocketServerWithRedisServerUtil\MainServer\Controller\Http;

use Imi\Server\Http\Controller\HttpController;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Server\ServerManager;
use Imi\Swoole\Server\Server;

/**
 * 服务器工具类.
 *
 * @Controller("/serverUtil/")
 */
class ServerUtilController extends HttpController
{
    /**
     * @Action
     */
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

    /**
     * @Action
     */
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

    /**
     * @Action
     */
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

    /**
     * @Action
     */
    public function sendToGroup(): array
    {
        $data = [
            'data'  => 'test',
        ];
        $dataStr = json_encode($data);
        $result = [
            // @phpstan-ignore-next-line
            'groupClientIdCount' => ServerManager::getServer('main')->getGroup('g1')->count(),
        ];

        $result['sendToGroup'] = Server::sendToGroup('g1', $data);
        $result['sendRawToGroup'] = Server::sendRawToGroup('g1', $dataStr);

        return $result;
    }

    /**
     * @Action
     *
     * @param int|string $clientId
     */
    public function exists($clientId, string $flag): array
    {
        return [
            'clientId'   => Server::exists($clientId),
            'flag'       => Server::flagExists($flag),
        ];
    }

    /**
     * @Action
     *
     * @param int|string $clientId
     */
    public function close($clientId, string $flag): array
    {
        return [
            'clientId'   => Server::close($clientId),
            'flag'       => Server::closeByFlag($flag),
        ];
    }
}
