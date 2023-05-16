<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util\Stream;

use Imi\Util\Stream\FileStream;
use Imi\Util\Stream\StreamMode;
use Imi\Util\Uri;
use Psr\Http\Message\StreamInterface;

class FileStreamUriTest extends FileStreamTest
{
    protected function newStream(): StreamInterface
    {
        return new FileStream(new Uri($this->getUrl()), StreamMode::READ_WRITE_CLEAN);
    }
}
