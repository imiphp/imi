<?php

namespace Imi\Grpc\Client\Contract;

use Imi\Rpc\Client\IService;

interface IGrpcService extends IService
{
    /**
     * 发送请求
     * 成功返回 streamId
     * $metadata 格式：['key' => ['value']].
     *
     * @param string                            $method
     * @param \Google\Protobuf\Internal\Message $message
     * @param array                             $metadata
     *
     * @return int|bool
     */
    public function send($method, \Google\Protobuf\Internal\Message $message, $metadata = []);

    /**
     * 接收响应结果.
     *
     * @param string     $responseClass
     * @param int        $streamId
     * @param float|null $timeout
     *
     * @return \Google\Protobuf\Internal\Message
     */
    public function recv($responseClass, $streamId = -1, $timeout = null);
}
