<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

class CacheApcuTest extends BaseCacheTest
{
    protected string $cacheName = 'apcu';

    protected function setUp(): void
    {
        if (!\extension_loaded('apcu') || !apcu_enabled())
        {
            $this->markTestSkipped();
        }
    }
}
