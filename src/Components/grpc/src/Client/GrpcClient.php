<?php

declare(strict_types=1);

namespace Imi\Grpc\Client;

use Imi\Bean\BeanFactory;
use Imi\Grpc\Parser;
use Imi\Log\Log;
use Imi\Rpc\Client\IRpcClient;
use Imi\Rpc\Client\IService;
use Imi\Util\Http\Consts\MediaType;
use Imi\Util\Uri;
use Yurun\Util\HttpRequest;
use Yurun\Util\YurunHttp\Http2\SwooleClient;

/**
 * gRPC 客户端.
 */
class GrpcClient implements IRpcClient
{
    /**
     * 配置.
     */
    protected array $options = [];

    /**
     * Http2 客户端.
     */
    protected ?SwooleClient $http2Client = null;

    /**
     * url.
     */
    protected string $url = '';

    /**
     * uri 对象
     */
    protected Uri $uri;

    /**
     * 请求方法.
     */
    protected string $requestMethod = '';

    /**
     * 超时时间，单位：秒.
     */
    protected ?float $timeout = null;

    /**
     * HttpRequest.
     */
    protected HttpRequest $httpRequest;

    /**
     * 构造方法.
     *
     * @param array $options 配置
     */
    public function __construct($options)
    {
        if (!isset($options['url']))
        {
            throw new \InvalidArgumentException('Missing [url] parameter');
        }
        $this->url = $options['url'];
        $this->uri = new Uri($this->url);
        $this->requestMethod = $options['method'] ?? 'GET';
        $this->timeout = $options['timeout'] ?? null;
        $this->options = $options;
    }

    /**
     * 打开
     */
    public function open(): bool
    {
        $http2Client = new SwooleClient($this->uri->getHost(), Uri::getServerPort($this->uri), 'https' === $this->uri->getScheme());
        $result = $http2Client->connect();
        if ($result)
        {
            if ($this->timeout)
            {
                $http2Client->setTimeout($this->timeout);
            }
            $this->httpRequest = new HttpRequest();
            $this->http2Client = $http2Client;
        }

        return $result;
    }

    /**
     * 关闭.
     */
    public function close(): void
    {
        if ($this->isConnected())
        {
            $this->http2Client->close();
        }
    }

    /**
     * 是否已连接.
     */
    public function isConnected(): bool
    {
        return isset($this->http2Client) && $this->http2Client->isConnected();
    }

    /**
     * 实时检查是否已连接.
     */
    public function checkConnected(): bool
    {
        return isset($this->http2Client) && $this->http2Client->ping();
    }

    /**
     * 获取实例对象
     */
    public function getInstance(): SwooleClient
    {
        return $this->http2Client;
    }

    /**
     * 获取服务对象
     *
     * @param string|null $name 服务名
     */
    public function getService(?string $name = null): IService
    {
        return BeanFactory::newInstance(GrpcService::class, $this, ...\func_get_args());
    }

    /**
     * 获取配置.
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * 发送请求
     *
     * $metadata 格式：['key' => ['value']]
     *
     * @return int|bool
     */
    public function send(string $package, string $service, string $name, \Google\Protobuf\Internal\Message $message, array $metadata = [])
    {
        if (!$this->isConnected())
        {
            throw new \RuntimeException('GrpcClient not connected');
        }
        $url = $this->buildRequestUrl($package, $service, $name);
        $content = Parser::serializeMessage($message);
        $request = $this->httpRequest->buildRequest($url, $content, $this->requestMethod)
        ->withHeader('Content-Type', MediaType::GRPC)
        ->withHeader('te', 'trailers');
        if ($metadata)
        {
            foreach ($metadata as $k => $v)
            {
                $request = $request->withHeader($k, $v);
            }
        }

        return $this->http2Client->send($request);
    }

    /**
     * 接收响应结果.
     */
    public function recv(string $responseClass, int $streamId = -1, ?float $timeout = null): \Google\Protobuf\Internal\Message
    {
        if (!$this->isConnected())
        {
            throw new \RuntimeException('GrpcClient not connected');
        }
        $result = $this->http2Client->recv($streamId, $timeout);
        if (!$result || !$result->success)
        {
            throw new \RuntimeException(sprintf('gRPC recv() failed, errCode:%s, errorMsg:%s', $result->getErrno(), $result->getError()));
        }
        $return = Parser::deserializeMessage([$responseClass, 'decode'], $result->body());
        if (!$return)
        {
            Log::debug(sprintf('GrpcClient deserializeMessage failed. statusCode: %s', $result->getStatusCode()));
        }

        return $return;
    }

    /**
     * 构建请求URL.
     */
    public function buildRequestUrl(string $package, string $service, string $name): string
    {
        // @phpstan-ignore-next-line
        return preg_replace_callback('/\{([^\|\}]+)\|?([^\}]*)\}/', static function ($match) use ($package, $service, $name) {
            $value = ${$match[1]} ?? '';
            if ('' !== $match[2])
            {
                $value = $match[2]($value);
            }

            return $value;
        }, $this->url);
    }
}
