<?php

declare(strict_types=1);

namespace AMQPApp\AMQP\Test2;

use Imi\AMQP\Message;

class TestMessage2 extends Message
{
    /**
     * 用户ID.
     *
     * @var int
     */
    private $memberId;

    /**
     * 内容.
     *
     * @var string
     */
    private $content;

    public function __construct()
    {
        parent::__construct();
        $this->routingKey = 'imi-2';
        $this->format = \Imi\Util\Format\Json::class;
    }

    /**
     * 设置主体数据.
     *
     * @param mixed $data
     *
     * @return self
     */
    public function setBodyData($data)
    {
        foreach ($data as $k => $v)
        {
            $this->$k = $v;
        }

        return $this;
    }

    /**
     * 获取主体数据.
     *
     * @return mixed
     */
    public function getBodyData()
    {
        return [
            'memberId'  => $this->memberId,
            'content'   => $this->content,
        ];
    }

    /**
     * Get 用户ID.
     *
     * @return int
     */
    public function getMemberId()
    {
        return $this->memberId;
    }

    /**
     * Set 用户ID.
     *
     * @param int $memberId 用户ID
     *
     * @return self
     */
    public function setMemberId(int $memberId)
    {
        $this->memberId = $memberId;

        return $this;
    }

    /**
     * Get 内容.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set 内容.
     *
     * @param string $content 内容
     *
     * @return self
     */
    public function setContent(string $content)
    {
        $this->content = $content;

        return $this;
    }
}
