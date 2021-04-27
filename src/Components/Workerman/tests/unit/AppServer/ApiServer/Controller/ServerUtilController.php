<?php

declare(strict_types=1);

namespace Imi\Workerman\Test\AppServer\ApiServer\Controller;

use Imi\Server\Http\Controller\HttpController;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Workerman\Server\Server;

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
        $server = Server::getServer('http');
        $result['http'] = $server->getName();
        $server = Server::getServer('not found');
        $result['notFound'] = null === $server;

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
        $result['send2'] = Server::send($data, $clientIds[0], 'websocket');
        $result['send3'] = Server::send($data, $clientIds, 'websocket');
        $result['sendByFlag'] = Server::sendByFlag($data, $flag, 'websocket');
        $result['sendRaw1'] = Server::sendRaw($dataStr);
        $result['sendRaw2'] = Server::sendRaw($dataStr, $clientIds[0], 'websocket');
        $result['sendRaw3'] = Server::sendRaw($dataStr, $clientIds, 'websocket');
        $result['sendRawByFlag'] = Server::sendRawByFlag($dataStr, $flag, 'websocket');

        $result['sendToAll'] = Server::sendToAll($data, 'websocket');
        $result['sendRawToAll'] = Server::sendRawToAll($dataStr, 'websocket');

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
        $result = [];

        $result['sendToGroup'] = Server::sendToGroup('g1', $data, 'websocket');
        $result['sendRawToGroup'] = Server::sendRawToGroup('g1', $dataStr, 'websocket');

        return $result;
    }

    /**
     * @Action
     */
    public function close(string $flag): array
    {
        return [
            'flag' => Server::closeByFlag($flag, 'websocket'),
        ];
    }
}
