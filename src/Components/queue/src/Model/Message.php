<?php

declare(strict_types=1);

namespace Imi\Queue\Model;

use Imi\Queue\Contract\IMessage;

/**
 * 消息.
 */
class Message implements IMessage
{
    /**
     * 消息 ID.
     */
    protected string $messageId = '';

    /**
     * 已重试次数.
     */
    protected int $retryCount = 0;

    /**
     * 最大重试次数.
     */
    protected int $maxRetryCount = 0;

    /**
     * 消息内容.
     */
    protected string $message;

    /**
     * 工作超时时间，单位：秒.
     */
    protected float $workingTimeout = 0;

    /**
     * {@inheritDoc}
     */
    public function getMessageId(): string
    {
        return $this->messageId;
    }

    /**
     * {@inheritDoc}
     */
    public function setMessageId(string $messageId): void
    {
        $this->messageId = $messageId;
    }

    /**
     * {@inheritDoc}
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * {@inheritDoc}
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * {@inheritDoc}
     */
    public function getWorkingTimeout(): float
    {
        return $this->workingTimeout;
    }

    /**
     * {@inheritDoc}
     */
    public function setWorkingTimeout(float $workingTimeout): void
    {
        $this->workingTimeout = (float) $workingTimeout;
    }

    /**
     * {@inheritDoc}
     */
    public function getRetryCount(): int
    {
        return $this->retryCount;
    }

    /**
     * {@inheritDoc}
     */
    public function setRetryCount(int $retryCount): void
    {
        $this->retryCount = (int) $retryCount;
    }

    /**
     * {@inheritDoc}
     */
    public function getMaxRetryCount(): int
    {
        return $this->maxRetryCount;
    }

    /**
     * {@inheritDoc}
     */
    public function setMaxRetryCount(int $maxRetryCount): void
    {
        $this->maxRetryCount = (int) $maxRetryCount;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'messageId'      => $this->getMessageId(),
            'retryCount'     => $this->getRetryCount(),
            'maxRetryCount'  => $this->getMaxRetryCount(),
            'message'        => $this->getMessage(),
            'workingTimeout' => $this->getWorkingTimeout(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function loadFromArray(array $data): void
    {
        foreach ($data as $k => $v)
        {
            $method = 'set' . ucfirst($k);
            if (method_exists($this, $method))
            {
                $this->$method($v);
            }
            else
            {
                $this->$k = $v;
            }
        }
    }
}
