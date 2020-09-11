<?php

namespace Imi\Test\Component\Tests;

use Imi\Log\Log;
use Imi\Log\LogLevel;
use Imi\Test\BaseTest;

/**
 * @testdox Log
 */
class LogTest extends BaseTest
{
    public function testLog()
    {
        ob_start();
        Log::alert('alert');
        Log::critical('critical');
        Log::debug('debug');
        Log::emergency('emergency');
        Log::error('error');
        Log::info('info');
        Log::notice('notice');
        Log::warning('warning');
        Log::log(LogLevel::INFO, 'info2');
        ob_end_clean();
        $this->assertTrue(true, 'Log fail');
    }
}
