<?php

namespace Test;

use Imi\App;

class TestContext
{
    /**
     * @return void
     */
    public static function set()
    {
        App::set('test', 'imi');
    }
}
