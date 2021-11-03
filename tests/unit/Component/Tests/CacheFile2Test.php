<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

use Imi\Util\File;
use PHPUnit\Framework\Assert;

/**
 * @testdox Cache File2 Handler
 */
class CacheFile2Test extends BaseCacheTest
{
    protected string $cacheName = 'file2';

    public function testSetAndGet(): void
    {
        parent::testSetAndGet();
        $path = File::path(sys_get_temp_dir() . '/imi-cache/', sha1('imi'));
        Assert::assertTrue(is_file($path));
    }
}
