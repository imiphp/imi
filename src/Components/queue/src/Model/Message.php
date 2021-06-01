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
     *
     * @var string
     */
    protected $messageId = '';

    /**
     * 已重试次数.
     *
     * @var int
     */
    protected $retryCount = 0;

    /**
     * 最大重试次数.
     *
     * @var int
     */
    protected $maxRetryCount = 0;

    /**
     * 消息内容.
     *
     * @var string
     */
    protected $message;

    /**
     * 工作超时时间，单位：秒.
     *
     * @var float
     */
    protected $workingTimeout = 0;

    /**
     * 获取消息 ID.
     */
    public function getMessageId(): string
    {
        return $this->messageId;
    }

    /**
     * 设置消息 ID.
     *
     * @return void
     */
    public function setMessageId(string $messageId)
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
     *
     * @return void
     */
    public function setMessage(string $message)
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
     * @return void
     */
    public function setWorkingTimeout(float $workingTimeout)
    {
        $this->workingTimeout = $workingTimeout;
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
     * @return void
     */
    public function setRetryCount(int $retryCount)
    {
        $this->retryCount = $retryCount;
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
     * @return void
     */
    public function setMaxRetryCount(int $maxRetryCount)
    {
        $this->maxRetryCount = $maxRetryCount;
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
     *
     * @return void
     */
    public function loadFromArray(array $data)
    {
        foreach ($data as $k => $v)
        {
            $this->$k = $v;
        }
    }
}
