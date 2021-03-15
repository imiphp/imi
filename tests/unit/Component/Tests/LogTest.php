<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

use Imi\Log\Log;
use Imi\Log\LogLevel;
use Imi\Test\BaseTest;

/**
 * @testdox Log
 */
class LogTest extends BaseTest
{
    public function testLog(): void
    {
        Log::alert('alert');
        Log::critical('critical');
        Log::debug('debug');
        Log::emergency('emergency');
        Log::error('error');
        Log::info('info');
        Log::notice('notice');
        Log::warning('warning');
        Log::log(LogLevel::INFO, 'info2');
        $this->assertTrue(true, 'Log fail');
    }
}
