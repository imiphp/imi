<?php

declare(strict_types=1);

namespace Imi\Util;

use Imi\App;
use Imi\Bean\ReflectionContainer;

class DelayClassCallable
{
    private ?bool $returnsReference = null;

    private ?object $instance = null;

    public function __construct(private readonly string $className, private readonly string $methodName, private readonly array $constructArgs = [])
    {
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    public function getInstance(): object
    {
        return $this->instance ??= App::getSingleton($this->className, ...$this->constructArgs);
    }

    public function returnsReference(): bool
    {
        return $this->returnsReference ??= ReflectionContainer::getMethodReflection($this->className, $this->methodName)->returnsReference();
    }

    public function &__invoke(mixed ...$args): mixed
    {
        if ($this->returnsReference())
        {
            return $this->getInstance()->{$this->methodName}(...$args);
        }
        else
        {
            $result = $this->getInstance()->{$this->methodName}(...$args);

            return $result;
        }
    }

    public function __serialize(): array
    {
        return [
            'className'        => $this->className,
            'methodName'       => $this->methodName,
            'constructArgs'    => $this->constructArgs,
        ];
    }

    public function __unserialize(array $data): void
    {
        ['className' => $this->className, 'methodName' => $this->methodName, 'constructArgs' => $this->constructArgs] = $data;
    }
}
