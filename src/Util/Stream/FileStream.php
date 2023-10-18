<?php

declare(strict_types=1);

namespace Imi\Util\Stream;

use Imi\Util\Uri;
use Psr\Http\Message\StreamInterface;

class FileStream implements StreamInterface, \Stringable
{
    /**
     * 文件Uri.
     */
    protected ?Uri $uri = null;

    /**
     * 流对象
     *
     * @var resource|bool|null
     */
    protected $stream = null;

    /**
     * @param string|resource|Uri $uri
     */
    public function __construct($uri,
        /**
         * 流访问类型.
         */
        protected string $mode = StreamMode::READ_WRITE)
    {
        if (\is_string($uri))
        {
            $this->uri = $uri = new Uri($uri);
        }
        elseif (\is_resource($uri))
        {
            $this->stream = $uri;
            $this->uri = new Uri($this->getMetadata('uri') ?? '');
        }
        elseif ($uri instanceof Uri)
        {
            $this->uri = $uri;
        }
        else
        {
            $uri = $this->uri;
        }
        if (!$this->stream)
        {
            $this->stream = fopen($uri->__toString(), $mode);
            if (false === $this->stream)
            {
                throw new \RuntimeException(sprintf('Open stream %s error', (string) $uri)); // @codeCoverageIgnore
            }
        }
    }

    public function __destruct()
    {
        if ($this->stream)
        {
            $this->close();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function __toString(): string
    {
        try
        {
            $this->rewind();

            return (string) stream_get_contents($this->stream);
        }
        // @codeCoverageIgnoreStart
        catch (\Throwable)
        {
            return '';
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * {@inheritDoc}
     */
    public function close(): void
    {
        fclose($this->stream);
        $this->stream = null;
    }

    /**
     * {@inheritDoc}
     */
    public function detach()
    {
        $stream = $this->stream;
        $this->stream = null;

        return $stream;
    }

    /**
     * {@inheritDoc}
     */
    public function getSize(): ?int
    {
        $stat = fstat($this->stream);
        if (false === $stat)
        {
            throw new \RuntimeException('Get stream size error'); // @codeCoverageIgnore
        }

        return $stat['size'];
    }

    /**
     * {@inheritDoc}
     */
    public function tell(): int
    {
        $result = ftell($this->stream);
        if (false === $result)
        {
            throw new \RuntimeException('Stream tell error'); // @codeCoverageIgnore
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function eof(): bool
    {
        return feof($this->stream);
    }

    /**
     * {@inheritDoc}
     */
    public function isSeekable(): bool
    {
        return (bool) $this->getMetadata('seekable');
    }

    /**
     * {@inheritDoc}
     */
    public function seek(int $offset, int $whence = \SEEK_SET): void
    {
        if (-1 === fseek($this->stream, $offset, $whence))
        {
            throw new \RuntimeException('Seek stream error'); // @codeCoverageIgnore
        }
    }

    /**
     * {@inheritDoc}
     */
    public function rewind(): void
    {
        if (!rewind($this->stream))
        {
            throw new \RuntimeException('Rewind stream failed'); // @codeCoverageIgnore
        }
    }

    /**
     * {@inheritDoc}
     */
    public function isWritable(): bool
    {
        return \in_array($this->mode, [
            StreamMode::WRITE_CLEAN,
            StreamMode::WRITE_END,
            StreamMode::CREATE_READ_WRITE,
            StreamMode::CREATE_WRITE,
            StreamMode::READ_WRITE,
            StreamMode::READ_WRITE_CLEAN,
            StreamMode::READ_WRITE_END,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function write(string $string): int
    {
        $result = fwrite($this->stream, $string);
        if (false === $result)
        {
            throw new \RuntimeException('Write stream failed'); // @codeCoverageIgnore
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function isReadable(): bool
    {
        return \in_array($this->mode, [
            StreamMode::READ_WRITE,
            StreamMode::READ_WRITE_CLEAN,
            StreamMode::READ_WRITE_END,
            StreamMode::READONLY,
            StreamMode::CREATE_READ_WRITE,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function read(int $length): string
    {
        $result = fread($this->stream, $length);
        if (false === $result)
        {
            throw new \RuntimeException('Read stream error'); // @codeCoverageIgnore
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function getContents(): string
    {
        $result = stream_get_contents($this->stream);
        if (false === $result)
        {
            throw new \RuntimeException('Stream getContents error'); // @codeCoverageIgnore
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function getMetadata(?string $key = null)
    {
        $result = stream_get_meta_data($this->stream);
        // @phpstan-ignore-next-line
        if (!$result)
        {
            throw new \RuntimeException('Stream getMetadata error'); // @codeCoverageIgnore
        }
        if (null === $key)
        {
            return $result;
        }
        elseif (isset($result[$key]))
        {
            return $result[$key];
        }
        else
        {
            return null; // @codeCoverageIgnore
        }
    }

    public function getUri(): Uri
    {
        return $this->uri;
    }
}
