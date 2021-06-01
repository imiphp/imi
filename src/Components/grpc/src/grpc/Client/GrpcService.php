<?php

declare(strict_types=1);

namespace Imi\Grpc\Client;

use Imi\Aop\Annotation\Inject;
use Imi\Grpc\Client\Contract\IGrpcService;
use Imi\Rpc\Client\IRpcClient;

class GrpcService implements IGrpcService
{
    /**
     * 客户端.
     *
     * @var \Imi\Grpc\Client\GrpcClient
     */
    protected $client;

    /**
     * 包名.
     *
     * @var string
     */
    protected $package;

    /**
     * 服务名称.
     *
     * @var string
     */
    protected $serviceName;

    /**
     * 完整服务名称.
     *
     * @var string
     */
    protected $name;

    /**
     * 服务接口.
     *
     * @var string|null
     */
    protected $interface;

    /**
     * @Inject("GrpcInterfaceManager")
     *
     * @var \Imi\Grpc\Util\GrpcInterfaceManager
     */
    protected $interfaceManager;

    /**
     * @param string      $name
     * @param string|null $interface
     */
    public function __construct(GrpcClient $client, $name, $interface = null)
    {
        $this->client = $client;
        $this->name = $name;
        $this->interface = $interface;
        if (preg_match('/^(.+)\.([^\.]+)$/', $name, $matches) > 0)
        {
            $this->package = $matches[1];
            $this->serviceName = $matches[2];
        }
    }

    /**
     * 获取服务名称.
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 发送请求
     * 成功返回 streamId
     * $metadata 格式：['key' => ['value']].
     *
     * @param string $method
     * @param array  $metadata
     *
     * @return int|bool
     */
    public function send($method, \Google\Protobuf\Internal\Message $message, $metadata = [])
    {
        return $this->client->send($this->package, $this->serviceName, $method, $message, $metadata);
    }

    /**
     * 接收响应结果.
     *
     * @param string     $responseClass
     * @param int        $streamId
     * @param float|null $timeout
     *
     * @return \Google\Protobuf\Internal\Message
     */
    public function recv($responseClass, $streamId = -1, $timeout = null)
    {
        return $this->client->recv($responseClass, $streamId, $timeout);
    }

    /**
     * 调用服务
     *
     * @param string $method 方法名
     * @param array  $args   参数
     *
     * @return mixed
     */
    public function call($method, $args = [])
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
     *
     * @return mixed
     */
    public function __call($name, $arguments)
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
