<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util\Stream;

use Imi\Test\BaseTest;
use Psr\Http\Message\StreamInterface;

abstract class BaseStreamTestCase extends BaseTest
{
    protected string $initContent = 'imi is very niu bi';

    protected string $writeContent = 'you can try on try';

    protected bool $seekable = true;

    protected bool $writable = true;

    protected bool $readable = true;

    public function test(): void
    {
        $stream = $this->newStream();
        $this->assertEquals($this->seekable, $stream->isSeekable());
        $this->assertEquals($this->writable, $stream->isWritable());
        $this->assertEquals($this->readable, $stream->isReadable());
        $this->assertEquals(\strlen($this->initContent), $stream->getSize());
        $this->assertEquals(0, $stream->tell());
        if (!$this->readable)
        {
            return;
        }

        // initContent
        $stream = $this->newStream();
        $this->assertEquals($this->initContent, (string) $stream);
        if ($this->seekable)
        {
            $stream->seek(0);
        }
        else
        {
            $stream = $this->newStream();
        }
        $this->assertEquals($this->initContent, $stream->getContents());

        // writeContent
        if ($this->writable)
        {
            $this->assertEquals(\strlen($this->writeContent), $stream->write($this->writeContent));
        }
        $this->assertTrue($stream->eof());
        if ($this->seekable)
        {
            $stream->seek(-1, \SEEK_END);
            $this->assertEquals(substr($this->writeContent, -1), $stream->read(1));

            $stream->seek(-\strlen($this->writeContent), \SEEK_END);
            $this->assertEquals($this->writeContent, $stream->getContents());

            $stream->rewind();
            $this->assertEquals(0, $stream->tell());
            $this->assertEquals($this->initContent . $this->writeContent, $stream->getContents());
        }
        if ($this->writable && $this->readable)
        {
            // 开头写入
            $stream->seek(0);
            $stream->write('a');
            $stream->seek(0);
            $this->assertEquals('a', $stream->read(1));

            // 中间写入
            $stream->seek(3, \SEEK_CUR);
            $stream->write('c');
            $stream->seek(-1, \SEEK_CUR);
            $this->assertEquals('c', $stream->read(1));

            // 结束写入
            $stream->seek(0, \SEEK_END);
            $stream->write('b');
            $stream->seek(-1, \SEEK_END);
            $this->assertEquals('b', $stream->read(1));
        }

        // close
        $stream = $this->newStream();
        $stream->close();
    }

    abstract protected function newStream(): StreamInterface;
}
