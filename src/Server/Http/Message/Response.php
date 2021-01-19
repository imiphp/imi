<?php

declare(strict_types=1);

namespace Imi\Server\Http\Message;

use Imi\Server\Http\Message\Contract\IHttpResponse;
use Imi\Util\Http\Consts\StatusCode;

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

    /**
     * 设置服务器端重定向
     * 默认状态码为302.
     *
     * @param string $url
     * @param int    $status
     *
     * @return static
     */
    public function redirect(string $url, int $status = StatusCode::FOUND): self
    {
        return $this->setStatus($status)->setHeader('location', $url);
    }
}
