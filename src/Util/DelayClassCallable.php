<?php

declare(strict_types=1);

namespace Imi\Util;

use Imi\App;

class DelayClassCallable
{
    private string $className = '';

    private string $methodName = '';

    private array $constructArgs = [];

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

    /**
     * @param mixed ...$args
     *
     * @return mixed
     */
    public function __invoke(...$args)
    {
        return App::getSingleton($this->className, ...$this->constructArgs)->{$this->methodName}(...$args);
    }
}
