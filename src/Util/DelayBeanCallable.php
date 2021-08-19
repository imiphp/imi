<?php

declare(strict_types=1);

namespace Imi\Util;

use Imi\App;

class DelayBeanCallable
{
    private string $beanName;

    private string $methodName;

    private array $constructArgs;

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

    /**
     * @param mixed ...$args
     *
     * @return mixed
     */
    public function __invoke(...$args)
    {
        return App::getBean($this->beanName, ...$this->constructArgs)->{$this->methodName}(...$args);
    }
}
