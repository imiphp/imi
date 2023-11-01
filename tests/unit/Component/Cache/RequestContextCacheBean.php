<?php

declare(strict_types=1);

namespace Imi\Test\Component\Cache;

use Imi\Cache\Annotation\Cacheable;

class RequestContextCacheBean
{
    #[Cacheable(name: 'requestContext')]
    public function getTime1(): int
    {
        return time();
    }

    #[Cacheable(name: 'requestContext', ttl: 1)]
    public function getTime2(): int
    {
        return time();
    }
}
