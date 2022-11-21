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
     * 消息内容.
     */
    protected string $message = '';

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
     *
     * @param float $workingTimeout
     */
    public function setWorkingTimeout($workingTimeout): void
    {
        $this->workingTimeout = (float) $workingTimeout;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'messageId'      => $this->getMessageId(),
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
                $this->{$method}($v);
            }
            else
            {
                $this->{$k} = $v;
            }
        }
    }
}
