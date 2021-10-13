<?php

declare(strict_types=1);

namespace Imi\Util\Stream;

use Imi\Util\Text;
use Psr\Http\Message\StreamInterface;

class MemoryStream implements StreamInterface
{
    /**
     * 内容.
     */
    protected string $content = '';

    /**
     * 大小.
     */
    protected int $size = 0;

    /**
     * 当前位置.
     */
    protected int $position = 0;

    public function __construct(string $content = '')
    {
        $this->content = $content;
        $this->size = \strlen($content);
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return $this->content;
    }

    /**
     * {@inheritDoc}
     */
    public function close(): void
    {
        $this->content = '';
        $this->size = -1;
    }

    /**
     * {@inheritDoc}
     */
    public function detach()
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * {@inheritDoc}
     */
    public function tell()
    {
        return $this->position;
    }

    /**
     * {@inheritDoc}
     */
    public function eof()
    {
        return $this->position > $this->size;
    }

    /**
     * {@inheritDoc}
     */
    public function isSeekable()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function seek($offset, $whence = \SEEK_SET)
    {
        switch ($whence)
        {
            case \SEEK_SET:
                if ($offset < 0)
                {
                    throw new \RuntimeException('Offset failure');
                }
                $this->position = $offset;
                break;
            case \SEEK_CUR:
                $this->position += $offset;
                break;
            case \SEEK_END:
                $this->position = $this->size - 1 + $offset;
                break;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function rewind(): void
    {
        $this->position = 0;
    }

    /**
     * {@inheritDoc}
     */
    public function isWritable()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function write($string)
    {
        $content = &$this->content;
        $position = &$this->position;
        $content = Text::insert($content, $position, $string);
        $len = \strlen($string);
        $position += $len;
        $this->size += $len;

        return $len;
    }

    /**
     * {@inheritDoc}
     */
    public function isReadable()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function read($length)
    {
        $position = &$this->position;
        $result = substr($this->content, $position, $length);
        $position += $length;

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function getContents()
    {
        $position = &$this->position;
        if (0 === $position)
        {
            $position = $this->size;

            return $this->content;
        }
        else
        {
            return $this->read($this->size - $position);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getMetadata($key = null)
    {
        return null;
    }
}
