<?php

declare(strict_types=1);

namespace Imi\Server\Http\Message;

use Imi\Server\Http\Message\Contract\IHttpResponse;
use Imi\Server\Http\Message\Emitter\Contract\IResponseBodyEmitter;
use Imi\Util\Http\Consts\StatusCode;

abstract class Response extends \Imi\Util\Http\Response implements IHttpResponse
{
    protected ?IResponseBodyEmitter $responseBodyEmitter = null;

    /**
     * {@inheritDoc}
     */
    public function redirect(string $url, int $status = StatusCode::FOUND): static
    {
        // @phpstan-ignore-next-line
        return $this->setStatus($status)->setHeader('location', $url);
    }

    public function withResponseBodyEmitter(?IResponseBodyEmitter $responseBodyEmitter): static
    {
        $clone = clone $this;
        $clone->responseBodyEmitter = $responseBodyEmitter;

        return $clone;
    }

    public function setResponseBodyEmitter(?IResponseBodyEmitter $responseBodyEmitter): static
    {
        $this->responseBodyEmitter = $responseBodyEmitter;

        return $this;
    }

    public function getResponseBodyEmitter(): ?IResponseBodyEmitter
    {
        return $this->responseBodyEmitter;
    }
}
