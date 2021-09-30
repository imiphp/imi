<?php

declare(strict_types=1);

namespace Imi\RoadRunner\Http\Message;

use Imi\Server\Http\Message\Request;
use Imi\Util\Http\Consts\MediaType;
use Imi\Util\Socket\IPEndPoint;
use Imi\Util\Uri;

class RoadRunnerRequest extends Request
{
    /**
     * 客户端地址
     */
    protected IPEndPoint $clientAddress;

    public function __construct(array $serverParams = [])
    {
        $this->server = $serverParams;
    }

    /**
     * 获取客户端地址
     */
    public function getClientAddress(): IPEndPoint
    {
        if (!isset($this->clientAddress))
        {
            return $this->clientAddress = new IPEndPoint($_SERVER['REMOTE_ADDR'], $_SERVER['REMOTE_PORT'] ?? 0);
        }

        return $this->clientAddress;
    }

    /**
     * 初始化 uri.
     */
    protected function initUri(): void
    {
        $this->uri = new Uri($_SERVER['REQUEST_URI']);
    }

    /**
     * {@inheritDoc}
     */
    public function withParsedBody($data)
    {
        $result = parent::withParsedBody($data);
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (MediaType::APPLICATION_FORM_URLENCODED === $contentType || str_starts_with($contentType, MediaType::MULTIPART_FORM_DATA))
        {
            $result->post = $_POST = $data;
        }

        return $result;
    }
}
