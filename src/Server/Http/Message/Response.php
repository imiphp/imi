<?php

declare(strict_types=1);

namespace Imi\Server\Http\Message;

use Imi\Server\Http\Message\Contract\IHttpResponse;

abstract class Response extends \Imi\Util\Http\Response implements IHttpResponse
{
    /**
     * 是否已结束请求
     *
     * @var bool
     */
    protected bool $isEnded = false;

    /**
     * 是否已结束请求
     *
     * @return bool
     */
    public function isEnded(): bool
    {
        return $this->isEnded;
    }
}
