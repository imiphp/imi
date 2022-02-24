<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Swoole\Server\Business\Task;

use GatewayWorker\Lib\Gateway;
use GatewayWorker\Protocols\GatewayProtocol;
use Imi\App;
use Imi\ConnectionContext;
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

if (\Imi\Util\Imi::checkAppType('swoole'))
{
    class WorkerTask implements ICoTask
    {
        /**
         * {@inheritDoc}
         */
        public function run(ITaskParam $param)
        {
            goWait(static function () use ($param) {
                $closeConnectionOnFail = false;
                try
                {
                    /** @var ISwooleServer $server */
                    /** @var IGatewayClient $client */
                    ['server' => $server, 'client' => $client, 'message' => $message, 'clientId' => $clientId] = $param->getData();
                    switch ($message['cmd']) {
                        case GatewayProtocol::CMD_ON_CONNECT:
                            // 连接
                            RequestContext::create([
                                'server'        => $server,
                                'clientId'      => $clientId,
                            ]);
                            ConnectionContext::create([
                                '__clientAddress' => long2ip($message['client_ip']),
                                '__clientPort'    => $message['client_port'],
                            ]);
                            $server->trigger('connect', [
                                'server'        => $server,
                                'clientId'      => $clientId,
                                'reactorId'     => 0,
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
                            $closeConnectionOnFail = true;
                            $swooleRequest = new \Swoole\Http\Request();
                            $swooleResponse = new \Swoole\Http\Response();
                            $request = new WorkermanGatewaySwooleRequest($server, $clientId, $message['body']);
                            $response = new SwooleResponse($server, $swooleResponse);
                            RequestContext::muiltiSet([
                                'server'         => $server,
                                'clientId'       => $clientId,
                                'swooleRequest'  => $swooleRequest,
                                'swooleResponse' => $swooleResponse,
                                'request'        => $request,
                                'response'       => $response,
                            ]);
                            ConnectionContext::set('uri', (string) $request->getUri());
                            $server->trigger('handShake', [
                                'request'   => $request,
                                'response'  => $response,
                            ], $server, HandShakeEventParam::class);
                            break;
                    }
                }
                catch (\Throwable $th)
                {
                    // @phpstan-ignore-next-line
                    App::getBean('ErrorLog')->onException($th);
                    if ($closeConnectionOnFail && isset($clientId))
                    {
                        Gateway::closeClient($clientId);
                    }
                }
            });
        }
    }
}
