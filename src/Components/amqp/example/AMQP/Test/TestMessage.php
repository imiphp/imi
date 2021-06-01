<?php

declare(strict_types=1);

namespace AMQPApp\AMQP\Test;

use Imi\AMQP\Message;

class TestMessage extends Message
{
    /**
     * 用户ID.
     *
     * @var int
     */
    private $memberId;

    public function __construct()
    {
        parent::__construct();
        $this->routingKey = 'imi-1';
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
}
