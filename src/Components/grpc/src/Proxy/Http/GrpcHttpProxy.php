<?php

declare(strict_types=1);

namespace Imi\Grpc\Proxy\Http;

use Imi\Aop\Annotation\Inject;
use Imi\Bean\Annotation\Bean;
use Imi\Grpc\Client\GrpcClient;
use Imi\Grpc\Client\GrpcService;
use Imi\Grpc\Enum\GrpcStatus;
use Imi\Grpc\Util\GrpcInterfaceManager;
use Imi\Grpc\Util\ProtobufUtil;
use Imi\Log\Log;
use Imi\Rpc\Client\Pool\RpcClientPool;
use Imi\Server\Http\Message\Contract\IHttpRequest;
use Imi\Server\Http\Message\Contract\IHttpResponse;
use Imi\Util\Http\Consts\ResponseHeader;
use Imi\Util\Http\Consts\StatusCode;

/**
 * @Bean(name="GrpcHttpProxy", recursion=false)
 */
class GrpcHttpProxy
{
    /**
     * @Inject("GrpcInterfaceManager")
     */
    protected GrpcInterfaceManager $grpcInterfaceManager;

    /**
     * @return mixed
     */
    public function proxy(string $poolName, IHttpRequest $request, IHttpResponse $response, string $serviceName, string $methodName, ?float $timeout = null)
    {
        try
        {
            $interface = $this->grpcInterfaceManager->getInterface($serviceName);
            if ('' === $interface)
            {
                throw new \RuntimeException(sprintf('Grpc service %s not found', $serviceName));
            }
            $requestClass = $this->grpcInterfaceManager->getRequest($interface, $methodName);
            if ('' === $requestClass)
            {
                throw new \RuntimeException(sprintf('Grpc service %s::%s() not found', $serviceName, $methodName));
            }

            // grpc request
            $grpcRequest = new $requestClass();
            if ($requestData = $request->request())
            {
                ProtobufUtil::setMessageData($grpcRequest, $requestData, true);
            }

            // metadata
            $metadata = [];
            foreach ($request->getHeaders() as $name => $_)
            {
                if (str_starts_with(strtolower((string) $name), 'grpc-'))
                {
                    $metadata[$name] = $request->getHeaderLine($name);
                }
            }

            /** @var GrpcClient $client */
            $client = RpcClientPool::getInstance($poolName);
            /** @var GrpcService $service */
            $service = $client->getService($serviceName, $interface);

            // send
            if (false === ($streamId = $service->send($methodName, $grpcRequest, $metadata)))
            {
                $response->setStatus(StatusCode::BAD_GATEWAY)
                         ->setHeader('grpc-status', (string) GrpcStatus::UNAVAILABLE)
                         ->setHeader('grpc-message', 'Send to grpc server failed');

                return null;
            }
            // recv
            $grpcResponse = $service->recv($this->grpcInterfaceManager->getResponse($interface, $methodName), $streamId, $timeout, $http2Response);

            // trailer
            $trailer = $http2Response->getHeaderLine(ResponseHeader::TRAILER);
            $response->setHeader(ResponseHeader::TRAILER, $trailer);
            foreach (explode(',', $trailer) as $trailerItem)
            {
                $trailerItem = trim($trailerItem);
                if ('' === $trailerItem)
                {
                    continue;
                }
                $response->setHeader($trailerItem, $http2Response->getHeaderLine($trailerItem));
            }

            // grpc response 转换
            return ProtobufUtil::getMessageValue($grpcResponse);
        }
        catch (\Throwable $th)
        {
            Log::error($th);
            $response->setStatus(StatusCode::BAD_GATEWAY)
                     ->setHeader('grpc-status', (string) GrpcStatus::UNKNOWN)
                     ->setHeader('grpc-message', $th->getMessage());

            return null;
        }
        finally
        {
            if (!isset($th))
            {
                $response->setHeader('grpc-status', (string) GrpcStatus::OK)
                         ->setHeader('grpc-message', '');
            }
        }
    }
}
