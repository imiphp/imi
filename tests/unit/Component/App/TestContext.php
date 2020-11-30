<?php

declare(strict_types=1);

namespace Test;

use Imi\App;

class TestContext
{
    public static function set()
    {
        App::set('test', 'imi');
    }
}
