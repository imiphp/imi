<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\HotUpdate;

use Imi\HotUpdate\Monitor\FileMTime;
use Imi\HotUpdate\Monitor\IMonitor;

class FileMTimeTest extends BaseMonitorTestCase
{
    protected function getMonitor(array $includePaths, array $excludePaths = []): IMonitor
    {
        return new FileMTime($includePaths, $excludePaths);
    }
}
