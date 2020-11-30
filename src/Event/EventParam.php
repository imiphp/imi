<?php

declare(strict_types=1);

namespace Imi\Event;

class EventParam
{
    /**
     * 事件名称.
     *
     * @var string
     */
    protected string $__eventName;

    /**
     * 触发该事件的对象
     *
     * @var mixed
     */
    protected $__target;

    /**
     * 数据.
     *
     * @var array
     */
    protected array $__data = [];

    /**
     * 阻止事件继续传播.
     *
     * @var bool
     */
    protected bool $__stopPropagation = false;

    public function __construct(string $eventName, array $data = [], $target = null)
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
     *
     * @return string
     */
    public function getEventName(): string
    {
        return $this->__eventName;
    }

    /**
     * 获取触发该事件的对象
     *
     * @return mixed
     */
    public function getTarget()
    {
        return $this->__target;
    }

    /**
     * 获取数据.
     *
     * @return array
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
    public function stopPropagation(bool $isStop = true)
    {
        $this->__stopPropagation = $isStop;
    }

    /**
     * 是否阻止事件继续传播.
     *
     * @return bool
     */
    public function isPropagationStopped(): bool
    {
        return $this->__stopPropagation;
    }
}
