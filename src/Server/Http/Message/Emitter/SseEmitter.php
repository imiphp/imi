<?php

declare(strict_types=1);

namespace Imi\Server\Http\Message\Emitter;

use Imi\Server\Http\Message\Contract\IHttpResponse;
use Imi\Server\Http\Message\Emitter\Contract\BaseEmitter;
use Imi\Server\Http\Message\Emitter\Handler\IEmitHandler;
use Imi\Util\Http\Consts\MediaType;
use Imi\Util\Http\Consts\ResponseHeader;

abstract class SseEmitter extends BaseEmitter
{
    private IEmitHandler $handler;

    public function init(IHttpResponse &$response, IEmitHandler $handler): void
    {
        $this->handler = $handler;
        $response->setHeader(ResponseHeader::CONTENT_TYPE, MediaType::TEXT_EVENT_STREAM)
                 ->setHeader(ResponseHeader::CACHE_CONTROL, 'no-cache')
                 ->setHeader(ResponseHeader::CONNECTION, 'Keep-Alive')
                 ->setHeader('X-Accel-Buffering', 'no') // 禁用 Nginx 缓冲区
        ;
    }

    public function getHandler(): IEmitHandler
    {
        return $this->handler;
    }

    public function send(): void
    {
        $this->task();
    }

    abstract protected function task(): void;
}
