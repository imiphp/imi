<?php

declare(strict_types=1);

namespace Imi\Grpc\Client\Contract;

use Imi\Rpc\Client\IService;

interface IGrpcService extends IService
{
    /**
     * 发送请求
     * 成功返回 streamId
     * $metadata 格式：['key' => ['value']].
     *
     * @return int|bool
     */
    public function send(string $method, \Google\Protobuf\Internal\Message $message, array $metadata = []);

    /**
     * 接收响应结果.
     */
    public function recv(string $responseClass, int $streamId = -1, ?float $timeout = null): \Google\Protobuf\Internal\Message;
}
