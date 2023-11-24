<?php

declare(strict_types=1);

namespace Imi\Core\Context;

class ContextData extends \ArrayObject
{
    protected \SplStack $deferCallbacks;

    public function __construct(mixed $input = [])
    {
        parent::__construct($input, self::ARRAY_AS_PROPS, \ArrayIterator::class);
        $this->deferCallbacks = new \SplStack();
    }

    /**
     * 推迟执行，当协程释放时触发，先进后出.
     */
    public function defer(callable $callback): void
    {
        $this->deferCallbacks[] = $callback;
    }

    /**
     * 获取推迟执行任务栈.
     */
    public function getDeferCallbacks(): \SplStack
    {
        return $this->deferCallbacks;
    }
}
