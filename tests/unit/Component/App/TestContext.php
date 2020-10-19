<?php

namespace Test;

use Imi\App;

class TestContext
{
    public static function set()
    {
        App::set('test', 'imi');
    }
}
