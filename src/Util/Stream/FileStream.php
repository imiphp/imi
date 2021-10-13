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
    protected Uri $uri;

    /**
     * 流对象
     *
     * @var resource|bool|null
     */
    protected $stream = null;

    /**
     * 流访问类型.
     */
    protected string $mode = '';

    /**
     * @param string|resource|Uri $uri
     */
    public function __construct($uri, string $mode = StreamMode::READ_WRITE)
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
        $this->mode = $mode;
        if (!$this->stream)
        {
            $this->stream = fopen($uri->__toString(), $mode);
            if (false === $this->stream)
            {
                throw new \RuntimeException(sprintf('Open stream %s error', (string) $uri));
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
    public function __toString()
    {
        try
        {
            $this->rewind();

            return stream_get_contents($this->stream);
        }
        catch (\Throwable $ex)
        {
            return '';
        }
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
    public function getSize()
    {
        $stat = fstat($this->stream);
        if (false === $stat)
        {
            throw new \RuntimeException('Get stream size error');
        }

        return $stat['size'];
    }

    /**
     * {@inheritDoc}
     */
    public function tell()
    {
        $result = ftell($this->stream);
        if (false === $result)
        {
            throw new \RuntimeException('Stream tell error');
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function eof()
    {
        return feof($this->stream);
    }

    /**
     * {@inheritDoc}
     */
    public function isSeekable()
    {
        return (bool) $this->getMetadata('seekable');
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function seek($offset, $whence = \SEEK_SET)
    {
        if (-1 === fseek($this->stream, $offset, $whence))
        {
            throw new \RuntimeException('Seek stream error');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function rewind(): void
    {
        if (!rewind($this->stream))
        {
            throw new \RuntimeException('Rewind stream failed');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function isWritable()
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
    public function write($string)
    {
        $result = fwrite($this->stream, $string);
        if (false === $result)
        {
            throw new \RuntimeException('Write stream failed');
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function isReadable()
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
    public function read($length)
    {
        $result = fread($this->stream, $length);
        if (false === $result)
        {
            throw new \RuntimeException('Read stream error');
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function getContents()
    {
        $result = stream_get_contents($this->stream);
        if (false === $result)
        {
            throw new \RuntimeException('Stream getContents error');
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function getMetadata($key = null)
    {
        $result = stream_get_meta_data($this->stream);
        // @phpstan-ignore-next-line
        if (!$result)
        {
            throw new \RuntimeException('Stream getMetadata error');
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
            return null;
        }
    }

    public function getUri(): Uri
    {
        return $this->uri;
    }
}
