<?php

declare(strict_types=1);

namespace Imi\Cron\Message;

class CommonMsg implements IMessage
{
    /**
     * 初始化.
     */
    public function __construct(
        /**
         * 从socket获取的返回信息.
         */
        protected mixed $response = []
    ) {
    }

    /**
     * 设置返回信息.
     */
    public function setResponse(mixed $response = []): self
    {
        $this->response = $response;

        return $this;
    }

    /**
     * 获取返回信息.
     */
    public function getResponse(): mixed
    {
        return $this->response;
    }
}
