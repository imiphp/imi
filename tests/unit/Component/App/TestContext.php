<?php

declare(strict_types=1);

namespace Imi\Test\Component\App;

use Imi\App;

class TestContext
{
    public static function set(): void
    {
        App::set('test', 'imi');
    }
}
