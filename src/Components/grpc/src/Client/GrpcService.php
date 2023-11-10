<?php

declare(strict_types=1);

namespace Imi\Grpc\Client;

use Imi\Aop\Annotation\Inject;
use Imi\Grpc\Client\Contract\IGrpcService;
use Imi\Grpc\Util\GrpcInterfaceManager;
use Imi\Rpc\Client\IRpcClient;

class GrpcService implements IGrpcService
{
    /**
     * 包名.
     */
    protected string $package = '';

    /**
     * 服务名称.
     */
    protected string $serviceName = '';

    #[Inject(name: 'GrpcInterfaceManager')]
    protected GrpcInterfaceManager $interfaceManager;

    public function __construct(
        /**
         * 客户端.
         */
        protected GrpcClient $client,
        /**
         * 完整服务名称.
         */
        protected string $name,
        /**
         * 服务接口.
         */
        protected ?string $interface = null)
    {
        if (preg_match('/^(.+)\.([^\.]+)$/', $name, $matches) > 0)
        {
            $this->package = $matches[1];
            $this->serviceName = $matches[2];
        }
    }

    /**
     * 获取服务名称.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * 发送请求
     * 成功返回 streamId
     * $metadata 格式：['key' => ['value']].
     */
    public function send(string $method, \Google\Protobuf\Internal\Message $message, array $metadata = []): int|bool
    {
        return $this->client->send($this->package, $this->serviceName, $method, $message, $metadata);
    }

    /**
     * 接收响应结果.
     */
    public function recv(string $responseClass, int $streamId = -1, ?float $timeout = null, ?\Yurun\Util\YurunHttp\Http\Response &$response = null): \Google\Protobuf\Internal\Message
    {
        return $this->client->recv($responseClass, $streamId, $timeout, $response);
    }

    /**
     * 调用服务
     *
     * @param string $method 方法名
     * @param array  $args   参数
     */
    public function call(string $method, array $args = []): mixed
    {
        $streamId = $this->send($method, $args[0] ?? null);
        if (!$streamId)
        {
            return false;
        }

        return $this->recv($this->interfaceManager->getResponse($this->interface, $method), $streamId);
    }

    /**
     * 魔术方法.
     *
     * @param string $name      方法名
     * @param array  $arguments 参数
     */
    public function __call(string $name, array $arguments): mixed
    {
        return $this->call($name, $arguments);
    }

    /**
     * 获取客户端对象
     *
     * @return \Imi\Grpc\Client\GrpcClient
     */
    public function getClient(): IRpcClient
    {
        return $this->client;
    }
}
