<?php

declare(strict_types=1);

namespace Imi\Aop\Model;

class AopItem
{
    /**
     * @var callable
     */
    private $callback;

    public function __construct(private readonly string $class, private readonly string $methodRule, callable $callback, private readonly int $priority = 0, private readonly array $options = [])
    {
        $this->callback = $callback;
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

    public function getClassMethodRule(): string
    {
        return $this->class . '::' . $this->methodRule;
    }
}
