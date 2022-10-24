<?php

declare(strict_types=1);

namespace Imi\Util;

use Imi\App;
use Imi\Bean\ReflectionContainer;

class DelayClassCallable
{
    private string $className = '';

    private string $methodName = '';

    private array $constructArgs = [];

    private ?bool $returnsReference = null;

    public function __construct(string $className, string $methodName, array $constructArgs = [])
    {
        $this->className = $className;
        $this->methodName = $methodName;
        $this->constructArgs = $constructArgs;
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
        return App::getSingleton($this->className, ...$this->constructArgs);
    }

    public function returnsReference(): bool
    {
        return $this->returnsReference ??= ReflectionContainer::getMethodReflection($this->className, $this->methodName)->returnsReference();
    }

    /**
     * @param mixed ...$args
     *
     * @return mixed
     */
    public function &__invoke(...$args)
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
}
