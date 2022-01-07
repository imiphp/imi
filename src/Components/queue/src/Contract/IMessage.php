<?php

declare(strict_types=1);

namespace Imi\Queue\Contract;

use Imi\Util\Interfaces\IArrayable;

/**
 * 消息接口.
 */
interface IMessage extends IArrayable
{
    /**
     * 获取消息 ID.
     */
    public function getMessageId(): ?string;

    /**
     * 设置消息 ID.
     */
    public function setMessageId(string $messageId): void;

    /**
     * 获取消息内容.
     */
    public function getMessage(): ?string;

    /**
     * 设置消息内容.
     */
    public function setMessage(string $message): void;

    /**
     * 获取工作超时时间，单位：秒.
     */
    public function getWorkingTimeout(): float;

    /**
     * 设置工作超时时间，单位：秒.
     */
    public function setWorkingTimeout(float $workingTimeout): void;

    /**
     * 获取已重试次数.
     */
    public function getRetryCount(): int;

    /**
     * 获取重试次数.
     */
    public function setRetryCount(int $retryCount): void;

    /**
     * 获取最大重试次数.
     */
    public function getMaxRetryCount(): int;

    /**
     * 获取最大重试次数.
     */
    public function setMaxRetryCount(int $maxRetryCount): void;

    /**
     * 从数组加载数据.
     */
    public function loadFromArray(array $data): void;
}
