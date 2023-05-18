<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\HotUpdate;

use Imi\HotUpdate\Monitor\IMonitor;
use Imi\HotUpdate\Monitor\Inotify;

class InotifyTest extends BaseMonitorTest
{
    public static function setUpBeforeClass(): void
    {
        if (!\extension_loaded('inotify'))
        {
            static::markTestSkipped('Extension \'inotify\' is not installed');
        }
    }

    protected function getMonitor(array $includePaths, array $excludePaths = []): IMonitor
    {
        return new Inotify($includePaths, $excludePaths);
    }
}
