<?php
namespace Imi\Util;

class Defer
{
    /**
     * 延迟执行的回调
     *
     * @var callable
     */
    private $callable;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * 调用回调，返回执行结果
     *
     * @return mixed
     */
    public function call()
    {
        return ($this->callable)();
    }
}