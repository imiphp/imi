<?php

declare(strict_types=1);

namespace Imi\Aop\Model;

class AopItem
{
    private string $class = '';

    private string $methodRule = '';

    private $callback;

    private int $priority = 0;

    private array $options = [];

    public function __construct(string $class, string $methodRule, callable $callback, int $priority = 0, array $options = [])
    {
        $this->class = $class;
        $this->methodRule = $methodRule;
        $this->callback = $callback;
        $this->priority = $priority;
        $this->options = $options;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getMethodRule(): string
    {
        return $this->methodRule;
    }

    public function getCallback(): callable
    {
        return $this->callback;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
