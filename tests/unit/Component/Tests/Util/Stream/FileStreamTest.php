<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util\Stream;

use Imi\Util\Stream\FileStream;
use Imi\Util\Stream\StreamMode;
use Psr\Http\Message\StreamInterface;

class FileStreamTest extends BaseStreamTestCase
{
    protected string $initContent = '';

    public function test(): void
    {
        parent::test();
        $stream = $this->newStream();
        $this->assertIsArray($stream->getMetadata());
        $this->assertIsResource($stream->detach());
        $this->assertEquals($this->getUrl(), (string) $stream->getUri());
    }

    protected function getUrl(): string
    {
        return 'file://' . ('Windows' === \PHP_OS_FAMILY ? '/' : '') . \dirname(__DIR__, 3) . '/.runtime/test.txt';
    }

    /**
     * @return FileStream
     */
    protected function newStream(): StreamInterface
    {
        return new FileStream($this->getUrl(), StreamMode::READ_WRITE_CLEAN);
    }
}
