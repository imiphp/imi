<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util\Http;

use Imi\Test\BaseTest;
use Imi\Util\Http\MessageUtil;

class MessageUtilTest extends BaseTest
{
    public function testHeadersToStringList(): void
    {
        $this->assertEquals([
            'test' => 'a, b, c',
        ], MessageUtil::headersToStringList([
            'test' => ['a', 'b', 'c'],
        ]));
    }
}
