<?php

declare(strict_types=1);

namespace Imi\Server\Http\Message;

use Imi\Server\Http\Message\Contract\IHttpResponse;
use Imi\Util\Http\Consts\StatusCode;

abstract class Response extends \Imi\Util\Http\Response implements IHttpResponse
{
    /**
     * {@inheritDoc}
     */
    public function redirect(string $url, int $status = StatusCode::FOUND): self
    {
        // @phpstan-ignore-next-line
        return $this->setStatus($status)->setHeader('location', $url);
    }
}
