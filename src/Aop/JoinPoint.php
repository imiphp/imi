<?php

declare(strict_types=1);

namespace Imi\Aop;

class JoinPoint
{
    /**
     * 切入点类型.
     */
    protected string $type = '';

    /**
     * 请求方法名.
     */
    protected string $method = '';

    /**
     * 请求参数.
     */
    protected array $args = [];

    /**
     * 连接点所在的目标对象
     */
    protected object $target;

    public function __construct(string $type, string $method, array &$args, object $target)
    {
        $this->type = $type;
        $this->method = $method;
        $this->args = &$args;
        $this->target = $target;
    }

    /**
     * 获取切入点类型.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * 获取请求方法名.
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * 获取请求参数.
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * 获取连接点所在的目标对象
     */
    public function getTarget(): object
    {
        return $this->target;
    }

    /**
     * 修改请求参数.
     *
     * @param array $args 请求参数
     */
    public function setArgs(array $args): void
    {
        $this->args = $args;
    }
}
