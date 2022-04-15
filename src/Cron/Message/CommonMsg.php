<?php

declare(strict_types=1);

namespace Imi\Cron\Message;

class CommonMsg implements IMessage
{
    /**
     * 从socket获取的返回信息.
     *
     * @var mixed
     */
    protected $response = [];

    /**
     * 初始化.
     *
     * @param mixed $response
     */
    public function __construct($response = [])
    {
        $this->response = $response;
    }

    /**
     * 设置返回信息.
     *
     * @param mixed $response
     *
     * @return Imi\Cron\Message
     */
    public function setResponse($response = [])
    {
        $this->response = $response;

        return $this;
    }

    /**
     * 获取返回信息.
     *
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }
}
