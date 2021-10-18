<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

use Imi\Util\File;
use Imi\Util\Imi;
use PHPUnit\Framework\Assert;

/**
 * @testdox Cache File2 Handler
 */
class CacheFile3 extends BaseCacheTest
{
    protected string $cacheName = 'file3';

    public function testSetAndGet(): void
    {
        parent::testSetAndGet();
        $path = File::path(Imi::getNamespacePath('Imi\Test\Component\.runtime\cache'), 'imi');
        Assert::assertTrue(is_file($path));
    }
}
