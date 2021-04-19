<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Swoole\Server\Business\Task;

use GatewayWorker\Protocols\GatewayProtocol;
use Imi\App;
use Imi\ConnectContext;
use Imi\RequestContext;
use Imi\Swoole\Http\Message\SwooleResponse;
use Imi\Swoole\Server\Contract\ISwooleServer;
use Imi\Swoole\Server\Event\Param\CloseEventParam;
use Imi\Swoole\Server\Event\Param\ConnectEventParam;
use Imi\Swoole\Server\Event\Param\HandShakeEventParam;
use Imi\Swoole\Server\Event\Param\MessageEventParam;
use Imi\WorkermanGateway\Swoole\Http\Message\WorkermanGatewaySwooleRequest;
use Swoole\WebSocket\Frame;
use Workerman\Gateway\Gateway\Contract\IGatewayClient;
use Yurun\Swoole\CoPool\Interfaces\ICoTask;
use Yurun\Swoole\CoPool\Interfaces\ITaskParam;
use function Yurun\Swoole\Coroutine\goWait;

class WorkerTask implements ICoTask
{
    /**
     * 执行任务
     *
     * @return mixed
     */
    public function run(ITaskParam $param)
    {
        goWait(function () use ($param) {
            try
            {
                $result = $param->getData();
                /** @var ISwooleServer $server */
                /** @var IGatewayClient $client */
                ['server' => $server, 'client' => $client, 'message' => $message, 'clientId' => $clientId] = $result;
                switch ($message['cmd']) {
                case GatewayProtocol::CMD_ON_CONNECT:
                    // 连接
                    $server->trigger('connect', [
                        'server'          => $server,
                        'clientId'        => $clientId,
                        'reactorId'       => 0,
                    ], $server, ConnectEventParam::class);
                    break;
                case GatewayProtocol::CMD_ON_MESSAGE:
                    $frame = new Frame();
                    $frame->fd = $clientId;
                    $frame->data = $message['body'];
                    $frame->finish = true;
                    RequestContext::create([
                        'server'        => $server,
                        'clientId'      => $clientId,
                    ]);
                    var_dump('message', $clientId, ConnectContext::get('uri'));
                    $server->trigger('message', [
                        'server'    => $server,
                        'frame'     => $frame,
                    ], $server, MessageEventParam::class);
                    break;
                case GatewayProtocol::CMD_ON_CLOSE:
                    $server->trigger('close', [
                        'server'          => $server,
                        'clientId'        => $clientId,
                        'reactorId'       => 0,
                    ], $server, CloseEventParam::class);
                    break;
                case GatewayProtocol::CMD_ON_WEBSOCKET_CONNECT:
                    $swooleRequest = new \Swoole\Http\Request();
                    $swooleResponse = new \Swoole\Http\Response();
                    $request = new WorkermanGatewaySwooleRequest($server, $message['body']);
                    $response = new SwooleResponse($server, $swooleResponse);
                    RequestContext::create([
                        'server'         => $server,
                        'swooleRequest'  => $swooleRequest,
                        'swooleResponse' => $swooleResponse,
                        'request'        => $request,
                        'response'       => $response,
                        'clientId'       => $clientId,
                    ]);
                    ConnectContext::create([
                        'uri' => (string) $request->getUri(),
                    ]);
                    var_dump('connect', $clientId, ConnectContext::get('uri'));
                    $server->trigger('handShake', [
                        'request'   => $request,
                        'response'  => $response,
                    ], $server, HandShakeEventParam::class);
                }
            }
            catch (\Throwable $th)
            {
                App::getBean('ErrorLog')->onException($th);
            }
        });
    }
}
