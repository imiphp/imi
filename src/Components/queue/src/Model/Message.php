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
     * 获取消息 ID.
     */
    public function getMessageId(): string
    {
        return $this->messageId;
    }

    /**
     * 设置消息 ID.
     */
    public function setMessageId(string $messageId): void
    {
        $this->messageId = $messageId;
    }

    /**
     * 获取消息内容.
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * 设置消息内容.
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * 获取工作超时时间，单位：秒.
     */
    public function getWorkingTimeout(): float
    {
        return $this->workingTimeout;
    }

    /**
     * 设置工作超时时间，单位：秒.
     *
     * @param float $workingTimeout
     */
    public function setWorkingTimeout($workingTimeout): void
    {
        $this->workingTimeout = (float) $workingTimeout;
    }

    /**
     * 获取已重试次数.
     */
    public function getRetryCount(): int
    {
        return $this->retryCount;
    }

    /**
     * 获取重试次数.
     *
     * @param int $retryCount
     */
    public function setRetryCount($retryCount): void
    {
        $this->retryCount = (int) $retryCount;
    }

    /**
     * 获取最大重试次数.
     */
    public function getMaxRetryCount(): int
    {
        return $this->maxRetryCount;
    }

    /**
     * 获取最大重试次数.
     *
     * @param int $maxRetryCount
     */
    public function setMaxRetryCount($maxRetryCount): void
    {
        $this->maxRetryCount = (int) $maxRetryCount;
    }

    /**
     * 将当前对象作为数组返回.
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
     * 从数组加载数据.
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
