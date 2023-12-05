<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util\Format;

use Imi\Util\Format\PhpSession;

class PhpSessionTest extends BaseFormatTest
{
    protected string $class = PhpSession::class;

    protected function setUp(): void
    {
        if (\PHP_VERSION_ID >= 80300)
        {
            $this->markTestSkipped();
        }
    }
}
