<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\HotUpdate;

use Imi\HotUpdate\Monitor\IMonitor;
use Imi\Test\BaseTest;
use Imi\Util\File;

abstract class BaseMonitorTestCase extends BaseTest
{
    public function test(): void
    {
        $path = \dirname(__DIR__, 2) . \DIRECTORY_SEPARATOR . '.runtime' . \DIRECTORY_SEPARATOR . 'hot-update';
        if (is_dir($path))
        {
            File::deleteDir($path);
        }
        File::createDir($path);
        $excludePath = $path . \DIRECTORY_SEPARATOR . '.exclude';
        $monitor = $this->getMonitor([$path], [$excludePath]);
        $this->assertFalse($monitor->isChanged());

        $file = $path . \DIRECTORY_SEPARATOR . 'test1.txt';
        file_put_contents($file, 'test1');
        $this->assertTrue($monitor->isChanged());
        $this->assertEquals([$file], $monitor->getChangedFiles());
        $this->assertFalse($monitor->isChanged());

        unlink($file);
        $this->assertTrue($monitor->isChanged());
        $this->assertEquals([$file], $monitor->getChangedFiles());
        $this->assertFalse($monitor->isChanged());

        mkdir($excludePath, 0o777, true);
        $this->assertFalse($monitor->isChanged());

        $file = $excludePath . \DIRECTORY_SEPARATOR . 'test1.txt';
        file_put_contents($file, 'test1');
        $this->assertFalse($monitor->isChanged());
    }

    abstract protected function getMonitor(array $includePaths, array $excludePaths = []): IMonitor;
}
