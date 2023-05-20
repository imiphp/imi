<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util\Stream;

use Imi\Util\Stream\MemoryStream;
use Psr\Http\Message\StreamInterface;

class MemoryStreamTest extends BaseStreamTest
{
    public function test(): void
    {
        parent::test();
        $stream = $this->newStream();
        $this->assertNull($stream->getMetadata());
        $this->assertNull($stream->detach());
    }

    protected function newStream(): StreamInterface
    {
        return new MemoryStream($this->initContent);
    }
}
