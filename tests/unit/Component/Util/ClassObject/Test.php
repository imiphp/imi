<?php

declare(strict_types=1);

namespace Imi\Test\Component\Util\ClassObject;

class Test
{
    /**
     * @var mixed
     */
    public $a;

    /**
     * @var mixed
     */
    public $b;

    public string $c;

    public function __construct(mixed $a, mixed $b, string $c = 'imi.com')
    {
        $this->a = $a;
        $this->b = $b;
        $this->c = $c;
    }

    public function imi(mixed $a, mixed $b, string $c = 'imi.com'): void
    {
    }

    public function noParam(): void
    {
    }

    public function variadic(string ...$params): void
    {
    }
}
