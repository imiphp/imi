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
     * {@inheritDoc}
     */
    public function setBodyData($data): self
    {
        foreach ($data as $k => $v)
        {
            $this->{$k} = $v;
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getBodyData(): mixed
    {
        return [
            'memberId'  => $this->memberId,
            'content'   => $this->content,
        ];
    }

    /**
     * Get 用户ID.
     */
    public function getMemberId(): int
    {
        return $this->memberId;
    }

    /**
     * Set 用户ID.
     *
     * @param int $memberId 用户ID
     */
    public function setMemberId(int $memberId): self
    {
        $this->memberId = $memberId;

        return $this;
    }

    /**
     * Get 内容.
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Set 内容.
     *
     * @param string $content 内容
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }
}
