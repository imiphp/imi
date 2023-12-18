<?php

declare(strict_types=1);

namespace Imi\Test\Component\Bean;

use Imi\App;

class InitClass
{
    private bool $throw;

    public function __construct()
    {
        $this->throw = App::get('InitClass.throw');
    }

    public function __init()
    {
        if ($this->throw)
        {
            throw new \RuntimeException('gg');
        }
    }

    public function isThrow(): bool
    {
        return $this->throw;
    }
}
