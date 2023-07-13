<?php

declare(strict_types=1);

namespace Imi\Grpc\Proxy\Http;

use Google\Protobuf\Internal\Message;
use Imi\Grpc\Enum\GrpcStatus;
use Imi\Grpc\Util\ProtobufUtil;
use Imi\Util\Http\Consts\MediaType;
use Yurun\Util\HttpRequest;
use Yurun\Util\YurunHttp\Http\Psr7\Consts\RequestMethod;
use Yurun\Util\YurunHttp\Http\Psr7\Consts\StatusCode;
use Yurun\Util\YurunHttp\Http\Response;

class GrpcHttpClient
{
    protected HttpRequest $httpRequest;

    protected string $url = '';

    protected string $requestMethod = '';

    public function __construct(string $url, string $requestMethod = RequestMethod::POST, ?int $timeout = null)
    {
        $this->url = $url;
        $this->httpRequest = $httpRequest = new HttpRequest();
        $this->requestMethod = $requestMethod;
        if (null !== $timeout)
        {
            $httpRequest->timeout($timeout);
        }
    }

    /**
     * 发起请求
     *
     * @template T of Message
     *
     * @param string                                   $service          服务名
     * @param string                                   $method           方法名
     * @param \Google\Protobuf\Internal\Message        $message          请求消息
     * @param class-string<T>                          $responseClass    响应消息类
     * @param array                                    $metadata         元数据
     * @param \Yurun\Util\YurunHttp\Http\Response|null $response         HTTP 响应对象
     * @param array|null                               $responseMetadata 响应元数据
     *
     * @return T
     */
    public function request(string $service, string $method, Message $message, string $responseClass, array $metadata = [], ?array &$responseMetadata = [], ?Response &$response = null): Message
    {
        $this->prepareHttpRequest($service, $method, $message, $metadata);
        $response = $this->httpRequest->send();
        if (!$response->success)
        {
            throw new \RuntimeException(sprintf('GrpcHttpClient request failed, error: [%s] %s', $response->errno(), $response->error()));
        }
        $responseMetadata = [];
        foreach ($response->getHeaders() as $name => $_)
        {
            $name = strtolower((string) $name);
            if (str_starts_with($name, 'grpc-'))
            {
                $responseMetadata[$name] = $response->getHeaderLine($name);
            }
        }
        if (StatusCode::OK !== $response->getStatusCode())
        {
            throw new \RuntimeException(sprintf('GrpcHttpClient request failed, status code: %s', $response->getStatusCode()));
        }
        $data = $response->json(true);
        if (!$data)
        {
            throw new \RuntimeException('GrpcHttpClient request failed, response json_decode failed');
        }
        $responseMessage = ProtobufUtil::newMessage($responseClass, $data);
        if (GrpcStatus::OK != ($responseMetadata['grpc-status'] ?? GrpcStatus::OK))
        {
            throw new \RuntimeException(sprintf('Grpc response failed, grpc-status: %s, grpc-message: %s', $responseMetadata['grpc-status'], $responseMetadata['grpc-message'] ?? ''));
        }

        return $responseMessage;
    }

    public function getHttpRequest(): HttpRequest
    {
        return $this->httpRequest;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getRequestMethod(): string
    {
        return $this->requestMethod;
    }

    /**
     * 准备请求.
     *
     * 如有特殊需求，可以继承本类并重写此方法
     */
    protected function prepareHttpRequest(string $service, string $method, Message $message, array $metadata): void
    {
        $httpRequest = $this->httpRequest;
        $httpRequest->method($this->requestMethod)
                    ->url($this->url . '/' . $service . '/' . $method)
                    ->requestBody(json_encode(ProtobufUtil::getMessageValue($message)))
                    ->contentType(MediaType::APPLICATION_JSON);
        if ($metadata)
        {
            foreach ($metadata as $k => $v)
            {
                $httpRequest->header((string) $k, $v);
            }
        }
    }
}
