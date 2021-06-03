<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Test\AppServer\ApiServer\Controller;

use Imi\Server\Http\Controller\HttpController;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Workerman\Server\Server;
use Swoole\Coroutine;

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
        $result['send2'] = Server::send($data, $clientIds[0], $this->getServerName());
        $result['send3'] = Server::send($data, $clientIds, $this->getServerName());
        $result['sendByFlag'] = Server::sendByFlag($data, $flag, $this->getServerName());
        $result['sendRaw2'] = Server::sendRaw($dataStr, $clientIds[0], $this->getServerName());
        $result['sendRaw3'] = Server::sendRaw($dataStr, $clientIds, $this->getServerName());
        $result['sendRawByFlag'] = Server::sendRawByFlag($dataStr, $flag, $this->getServerName());

        $result['sendToAll'] = Server::sendToAll($data, $this->getServerName());
        $result['sendRawToAll'] = Server::sendRawToAll($dataStr, $this->getServerName());

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

        $result['sendToGroup'] = Server::sendToGroup('g1', $data, $this->getServerName());
        $result['sendRawToGroup'] = Server::sendRawToGroup('g1', $dataStr, $this->getServerName());

        return $result;
    }

    /**
     * @Action
     */
    public function close(string $flag): array
    {
        return [
            'flag' => Server::closeByFlag($flag, $this->getServerName()),
        ];
    }

    private function getServerName(): string
    {
        return Coroutine::getuid() > 0 ? 'main' : 'websocket';
    }
}
