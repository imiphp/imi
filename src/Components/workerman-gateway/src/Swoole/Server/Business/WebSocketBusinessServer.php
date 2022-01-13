<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Swoole\Server\Business;

use GatewayWorker\Lib\Context;
use GatewayWorker\Lib\Gateway;
use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Event\Event;
use Imi\Log\ErrorLog;
use Imi\Server\Server;
use Imi\Util\Socket\IPEndPoint;
use Imi\Worker;
use Imi\WorkermanGateway\Swoole\Server\Business\Task\WorkerTask;
use Swoole\Coroutine;
use Throwable;
use Workerman\Gateway\Config\GatewayWorkerConfig;
use Workerman\Gateway\Gateway\Contract\IGatewayClient;
use Workerman\Gateway\Gateway\GatewayWorkerClient;
use Yurun\Swoole\CoPool\CoPool;

if (\Imi\Util\Imi::checkAppType('swoole'))
{
    /**
     * @Bean("SwooleGatewayWebSocketBusinessServer")
     */
    class WebSocketBusinessServer extends \Imi\Swoole\Server\WebSocket\Server
    {
        /**
         * 请求处理工作池.
         */
        protected CoPool $pool;

        /**
         * {@inheritDoc}
         */
        protected function getServerInitConfig(): array
        {
            return [
                'host'      => $this->config['host'] ?? '127.0.0.1',
                'port'      => $this->config['port'] ?? 0,
                'sockType'  => $this->config['sockType'] ?? \SWOOLE_SOCK_TCP,
                'mode'      => $this->config['mode'] ?? \SWOOLE_BASE,
            ];
        }

        /**
         * {@inheritDoc}
         */
        public function __construct(string $name, array $config)
        {
            parent::__construct($name, $config);
            Event::on('IMI.MAIN_SERVER.WORKER.START', function () {
                $this->initGatewayWorker();
            });
        }

        protected function initGatewayWorker(): void
        {
            $workermanGatewayConfig = $this->config['workermanGateway'] ?? [];
            if (isset($workermanGatewayConfig['registerAddress']))
            {
                Gateway::$registerAddress = $workermanGatewayConfig['registerAddress'];
            }

            Coroutine::create(function () use ($workermanGatewayConfig) {
                $this->pool = $pool = new CoPool($workermanGatewayConfig['worker_coroutine_num'] ?? swoole_cpu_num(), $workermanGatewayConfig['channel']['size'] ?? 1024, WorkerTask::class);
                $pool->run();
                $pool->wait();
            });

            Coroutine::create(function () use ($workermanGatewayConfig) {
                $config = new GatewayWorkerConfig($workermanGatewayConfig);

                // Gateway Worker
                $client = new GatewayWorkerClient(($workermanGatewayConfig['workerName'] ?? $this->getName()) . ':' . Worker::getWorkerId(), $config);
                // 异常处理
                $client->onException = static function (Throwable $th) {
                    /** @var ErrorLog $errorLog */
                    $errorLog = App::getBean('ErrorLog');
                    $errorLog->onException($th);
                };
                // 网关消息
                $client->onGatewayMessage = function (IGatewayClient $client, array $message) {
                    $clientId = Context::addressToClientId($message['local_ip'], $message['local_port'], $message['connection_id']);
                    $this->pool->addTaskAsync([
                        'server'   => $this,
                        'client'   => $client,
                        'message'  => $message,
                        'clientId' => $clientId,
                    ], null, $clientId);
                };
                $client->run();
            });
        }

        /**
         * {@inheritDoc}
         */
        public function push($clientId, string $data, int $opcode = 1): bool
        {
            return Server::sendRaw($data, $clientId, $this->getName()) > 0;
        }

        /**
         * {@inheritDoc}
         */
        public function getClientAddress($clientId): IPEndPoint
        {
            $session = Gateway::getSession($clientId);

            return new IPEndPoint($session['__clientAddress'], $session['__clientPort']);
        }
    }
}
