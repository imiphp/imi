<?php
namespace Imi\Test\Tests;

use Imi\Test\BaseTest;
use Imi\App;

class InjectValueTest extends BaseTest
{
    public function testInject()
    {
        $test = App::getBean('TestInjectValue');
        $test->test();
    }

}