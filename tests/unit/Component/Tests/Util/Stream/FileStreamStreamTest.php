<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util\Stream;

use Imi\Util\Stream\FileStream;
use Imi\Util\Stream\StreamMode;
use Psr\Http\Message\StreamInterface;

class FileStreamStreamTest extends FileStreamTest
{
    protected function newStream(): StreamInterface
    {
        $fp = fopen($this->getUrl(), StreamMode::READ_WRITE_CLEAN);

        return new FileStream($fp, StreamMode::READ_WRITE_CLEAN);
    }
}
