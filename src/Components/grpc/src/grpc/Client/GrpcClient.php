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
     *
     * @var array
     */
    protected $options;

    /**
     * Http2 客户端.
     *
     * @var \Yurun\Util\YurunHttp\Http2\SwooleClient
     */
    protected $http2Client;

    /**
     * url.
     *
     * @var string
     */
    protected $url;

    /**
     * uri 对象
     *
     * @var \Imi\Util\Uri
     */
    protected $uri;

    /**
     * 请求方法.
     *
     * @var string
     */
    protected $requestMethod;

    /**
     * 超时时间，单位：秒.
     *
     * @var float
     */
    protected $timeout;

    /**
     * HttpRequest.
     *
     * @var \Yurun\Util\HttpRequest
     */
    protected $httpRequest;

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
        $this->httpRequest = new HttpRequest();
        $this->http2Client = new SwooleClient($this->uri->getHost(), Uri::getServerPort($this->uri), 'https' === $this->uri->getScheme());
        $result = $this->http2Client->connect();
        if ($result && $this->timeout)
        {
            $this->http2Client->setTimeout($this->timeout);
        }

        return $result;
    }

    /**
     * 关闭.
     */
    public function close(): void
    {
        $this->http2Client->close();
    }

    /**
     * 是否已连接.
     */
    public function isConnected(): bool
    {
        return $this->http2Client->isConnected();
    }

    /**
     * 获取实例对象
     *
     * @return \Yurun\Util\YurunHttp\Http2\SwooleClient
     */
    public function getInstance()
    {
        return $this->http2Client;
    }

    /**
     * 获取服务对象
     *
     * @param string $name 服务名
     */
    public function getService($name = null): IService
    {
        return BeanFactory::newInstance(GrpcService::class, $this, ...\func_get_args());
    }

    /**
     * 获取配置.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * 发送请求
     *
     * $metadata 格式：['key' => ['value']]
     *
     * @param string $package
     * @param string $service
     * @param string $name
     * @param array  $metadata
     *
     * @return int|bool
     */
    public function send($package, $service, $name, \Google\Protobuf\Internal\Message $message, $metadata = [])
    {
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
     *
     * @param string     $responseClass
     * @param int        $streamId
     * @param float|null $timeout
     *
     * @return \Google\Protobuf\Internal\Message
     */
    public function recv($responseClass, $streamId = -1, $timeout = null)
    {
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
     *
     * @param string $package
     * @param string $service
     * @param string $name
     *
     * @return string
     */
    public function buildRequestUrl($package, $service, $name)
    {
        // @phpstan-ignore-next-line
        return preg_replace_callback('/\{([^\|\}]+)\|?([^\}]*)\}/', function ($match) use ($package, $service, $name) {
            $value = ${$match[1]} ?? '';
            if ('' !== $match[2])
            {
                $value = $match[2]($value);
            }

            return $value;
        }, $this->url);
    }
}
