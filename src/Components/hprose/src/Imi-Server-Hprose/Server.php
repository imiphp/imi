<?php

declare(strict_types=1);

namespace Imi\Server\Hprose;

use Imi\App;
use Imi\Event\EventParam;
use Imi\Log\Log;
use Imi\Pool\PoolManager;
use Imi\RequestContext;
use Imi\Rpc\BaseRpcServer;
use Imi\Server\Protocol;
use Imi\Server\ServerManager;
use Imi\Swoole\Server\Event\Param\CloseEventParam;
use Imi\Swoole\Server\Event\Param\ConnectEventParam;
use Imi\Swoole\Server\Event\Param\ReceiveEventParam;

class Server extends BaseRpcServer
{
    /**
     * Hprose Service.
     */
    private \Hprose\Swoole\Socket\Service $hproseService;

    private bool $isHookHproseOn = false;

    /**
     * 创建 swoole 服务器对象
     */
    protected function createServer(): void
    {
        $config = $this->getServerInitConfig();
        $this->swooleServer = new \swoole_server($config['host'], $config['port'], $config['mode'], $config['sockType']);
        $this->hproseService = new \Hprose\Swoole\Socket\Service();
        $this->parseConfig($config);
    }

    /**
     * 从主服务器监听端口，作为子服务器.
     */
    protected function createSubServer(): void
    {
        $config = $this->getServerInitConfig();
        /* @phpstan-ignore-next-line */
        $this->swooleServer = ServerManager::getServer('main')->getSwooleServer();
        $this->swoolePort = $this->swooleServer->addListener($config['host'], $config['port'], $config['sockType']);
        $this->swoolePort->set([]);
        $this->hproseService = new \Hprose\Swoole\Socket\Service();
        $this->parseConfig($config);
        $this->hproseService->onBeforeInvoke = function ($name, &$args, $byref, \stdClass $context) {
            $requestContext = RequestContext::create();
            $requestContext['server'] = $this;
            $this->trigger('BeforeInvoke', [
                'name'      => $name,
                'args'      => &$args,
                'byref'     => $byref,
                'context'   => $context,
            ], $this);
        };
        $this->hproseService->onAfterInvoke = function ($name, $args, $byref, &$result, \stdClass $context) {
            $this->trigger('AfterInvoke', [
                'name'      => $name,
                'args'      => $args,
                'byref'     => $byref,
                'context'   => $context,
                'result'    => &$result,
            ], $this);
            if ($result instanceof \Imi\Model\BaseModel)
            {
                $result = $result->toArray();
            }
            PoolManager::destroyCurrentContext();
            RequestContext::destroy();
        };
        $this->hproseService->onSendError = function (\Throwable $error, \stdClass $context) {
            Log::error($error->getMessage(), [
                'trace'     => $error->getTrace(),
                'errorFile' => $error->getFile(),
                'errorLine' => $error->getLine(),
            ]);
            PoolManager::destroyCurrentContext();
            RequestContext::destroy();
        };
    }

    /**
     * 获取服务器初始化需要的配置.
     */
    protected function getServerInitConfig(): array
    {
        return [
            'host'      => isset($this->config['host']) ? $this->config['host'] : '0.0.0.0',
            'port'      => isset($this->config['port']) ? $this->config['port'] : 8080,
            'sockType'  => isset($this->config['sockType']) ? (\SWOOLE_SOCK_TCP | $this->config['sockType']) : \SWOOLE_SOCK_TCP,
            'mode'      => isset($this->config['mode']) ? $this->config['mode'] : \SWOOLE_PROCESS,
        ];
    }

    /**
     * 处理服务器配置.
     */
    private function parseConfig(array $config): void
    {
        if (\SWOOLE_UNIX_STREAM !== $config['sockType'])
        {
            $this->config['configs']['open_tcp_nodelay'] = true;
        }
        $this->config['configs']['open_eof_check'] = false;
        $this->config['configs']['open_length_check'] = false;
        $this->config['configs']['open_eof_split'] = false;
    }

    /**
     * 事件监听.
     *
     * @param string $name     事件名称
     * @param mixed  $callback 回调，支持回调函数、基于IEventListener的类名
     * @param int    $priority 优先级，越大越先执行
     */
    public function on($name, $callback, int $priority = 0): void
    {
        if ($this->isHookHproseOn)
        {
            parent::on($name, function (EventParam $e) use ($callback) {
                $data = $e->getData();
                $data['server'] = $this->swooleServer;
                $callback(...array_values($data));
            }, $priority);
        }
        else
        {
            parent::on($name, $callback, $priority);
        }
    }

    /**
     * 绑定服务器事件.
     */
    protected function __bindEvents(): void
    {
        $server = $this->swoolePort ?? $this->swooleServer;

        $this->isHookHproseOn = true;
        $this->hproseService->socketHandle($this);
        $this->isHookHproseOn = false;

        $server->on('connect', function (\swoole_server $server, $fd, $reactorId) {
            try
            {
                $this->trigger('connect', [
                    'server'    => $this,
                    'clientId'  => $fd,
                    'reactorId' => $reactorId,
                ], $this, ConnectEventParam::class);
            }
            catch (\Throwable $ex)
            {
                App::getBean('ErrorLog')->onException($ex);
            }
        });

        $server->on('receive', function (\swoole_server $server, $fd, $reactorId, $data) {
            try
            {
                $this->trigger('receive', [
                    'server'    => $this,
                    'clientId'  => $fd,
                    'reactorId' => $reactorId,
                    'data'      => $data,
                ], $this, ReceiveEventParam::class);
            }
            catch (\Throwable $ex)
            {
                App::getBean('ErrorLog')->onException($ex);
            }
        });

        $server->on('close', function (\swoole_server $server, $fd, $reactorId) {
            try
            {
                $this->trigger('close', [
                    'server'    => $this,
                    'clientId'  => $fd,
                    'reactorId' => $reactorId,
                ], $this, CloseEventParam::class);
            }
            catch (\Throwable $ex)
            {
                App::getBean('ErrorLog')->onException($ex);
            }
        });
    }

    /**
     * Get hprose Service.
     */
    public function getHproseService(): \Hprose\Swoole\Socket\Service
    {
        return $this->hproseService;
    }

    /**
     * 获取 RPC 类型.
     */
    public function getRpcType(): string
    {
        return 'Hprose';
    }

    /**
     * 获取控制器注解类.
     */
    public function getControllerAnnotation(): string
    {
        return \Imi\Rpc\Route\Annotation\RpcController::class;
    }

    /**
     * 获取动作注解类.
     */
    public function getActionAnnotation(): string
    {
        return \Imi\Rpc\Route\Annotation\RpcAction::class;
    }

    /**
     * 获取路由注解类.
     */
    public function getRouteAnnotation(): string
    {
        return \Imi\Hprose\Route\Annotation\HproseRoute::class;
    }

    /**
     * 获取路由处理类.
     */
    public function getRouteClass(): string
    {
        return 'HproseRoute';
    }

    /**
     * 是否为长连接服务
     */
    public function isLongConnection(): bool
    {
        return true;
    }

    /**
     * 是否支持 SSL.
     */
    public function isSSL(): bool
    {
        return false;
    }

    /**
     * 获取协议名称.
     */
    public function getProtocol(): string
    {
        return Protocol::TCP;
    }
}
