<?php

declare(strict_types=1);

namespace Imi\Event;

class EventParam
{
    /**
     * 事件名称.
     */
    protected string $__eventName = '';

    /**
     * 触发该事件的对象
     */
    protected ?object $__target;

    /**
     * 数据.
     */
    protected array $__data = [];

    /**
     * 阻止事件继续传播.
     */
    protected bool $__stopPropagation = false;

    public function __construct(string $eventName, array $data = [], ?object $target = null)
    {
        $this->__eventName = $eventName;
        $this->__target = $target;
        $this->__data = $data;
        foreach ($data as $key => &$value)
        {
            $this->$key = &$value;
        }
    }

    /**
     * 获取事件名称.
     */
    public function getEventName(): string
    {
        return $this->__eventName;
    }

    /**
     * 获取触发该事件的对象
     */
    public function getTarget(): ?object
    {
        return $this->__target;
    }

    /**
     * 获取数据.
     */
    public function getData(): array
    {
        return $this->__data;
    }

    /**
     * 阻止事件继续传播.
     *
     * @param bool $isStop 是否阻止事件继续传播
     */
    public function stopPropagation(bool $isStop = true): void
    {
        $this->__stopPropagation = $isStop;
    }

    /**
     * 是否阻止事件继续传播.
     */
    public function isPropagationStopped(): bool
    {
        return $this->__stopPropagation;
    }
}
