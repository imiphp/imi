<?php

declare(strict_types=1);

namespace Imi\Util;

use Imi\App;
use Imi\Bean\BeanFactory;
use Imi\Bean\ReflectionContainer;

class DelayBeanCallable
{
    private string $beanName = '';

    private string $methodName = '';

    private array $constructArgs = [];

    private ?bool $returnsReference = null;

    public function __construct(string $beanName, string $methodName, array $constructArgs = [])
    {
        $this->beanName = $beanName;
        $this->methodName = $methodName;
        $this->constructArgs = $constructArgs;
    }

    public function getBeanName(): string
    {
        return $this->beanName;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    public function getInstance(): object
    {
        return App::getBean($this->beanName, ...$this->constructArgs);
    }

    public function returnsReference(): bool
    {
        return $this->returnsReference ??= ReflectionContainer::getMethodReflection(BeanFactory::getObjectClass($this->getInstance()), $this->methodName)->returnsReference();
    }

    /**
     * @param mixed ...$args
     *
     * @return mixed
     */
    public function __invoke(...$args)
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
