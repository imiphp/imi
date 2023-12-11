<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Swoole\Server\Business\Task;

use GatewayWorker\Lib\Gateway;
use GatewayWorker\Protocols\GatewayProtocol;
use Imi\ConnectionContext;
use Imi\Log\Log;
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
    abstract class WorkerTask implements ICoTask
    {
        protected string $errorHandler = '';

        /**
         * {@inheritDoc}
         */
        public function run(ITaskParam $param): void
        {
            $errorHandler = $this->errorHandler;
            goWait(static function () use ($param, $errorHandler): void {
                $closeConnectionOnFail = false;
                $cmd = null;
                try
                {
                    /** @var ISwooleServer $server */
                    /** @var IGatewayClient $client */
                    ['server' => $server, 'client' => $client, 'message' => $message, 'clientId' => $clientId] = $param->getData();
                    switch ($cmd = $message['cmd'])
                    {
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
                            $server->dispatch(new ConnectEventParam($server, $clientId, 0));
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
                            $server->dispatch(new MessageEventParam($server, $frame));
                            break;
                        case GatewayProtocol::CMD_ON_CLOSE:
                            $server->dispatch(new CloseEventParam($server, $clientId, 0));
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
                            $server->dispatch(new HandShakeEventParam($server, $request, $response));
                            break;
                    }
                }
                catch (\Throwable $th)
                {
                    // @phpstan-ignore-next-line
                    if (GatewayProtocol::CMD_ON_MESSAGE === $cmd && isset($server) && true !== $server->getBean($errorHandler)->handle($th))
                    {
                        Log::error($th);
                    }
                    if ($closeConnectionOnFail && isset($clientId))
                    {
                        Gateway::closeClient($clientId);
                    }
                }
            });
        }
    }
}
