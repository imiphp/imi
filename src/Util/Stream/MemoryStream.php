<?php

declare(strict_types=1);

namespace Imi\Util\Stream;

use Imi\Util\Text;
use Psr\Http\Message\StreamInterface;

class MemoryStream implements StreamInterface, \Stringable
{
    /**
     * 大小.
     */
    protected int $size = 0;

    /**
     * 当前位置.
     */
    protected int $position = 0;

    public function __construct(/**
     * 内容.
     */
    protected string $content = '')
    {
        $this->size = \strlen($content);
    }

    /**
     * {@inheritDoc}
     */
    public function __toString(): string
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
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * {@inheritDoc}
     */
    public function tell(): int
    {
        return $this->position;
    }

    /**
     * {@inheritDoc}
     */
    public function eof(): bool
    {
        return $this->position >= $this->size - 1;
    }

    /**
     * {@inheritDoc}
     */
    public function isSeekable(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function seek(int $offset, int $whence = \SEEK_SET): void
    {
        switch ($whence)
        {
            case \SEEK_SET:
                if ($offset < 0)
                {
                    throw new \RuntimeException('Offset failure'); // @codeCoverageIgnore
                }
                $this->position = $offset;
                break;
            case \SEEK_CUR:
                $this->position += $offset;
                break;
            case \SEEK_END:
                $this->position = $this->size + $offset;
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
    public function isWritable(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function write(string $string): int
    {
        $content = &$this->content;
        $position = &$this->position;
        $size = &$this->size;
        if ($position >= $size)
        {
            $content .= $string;
        }
        elseif (0 === $position)
        {
            $content = $string . $content;
        }
        else
        {
            $content = Text::insert($content, $position, $string);
        }
        $len = \strlen($string);
        $position += $len;
        $size += $len;

        return $len;
    }

    /**
     * {@inheritDoc}
     */
    public function isReadable(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function read(int $length): string
    {
        $position = &$this->position;
        $result = substr($this->content, $position, $length);
        $position += $length;

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function getContents(): string
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
    public function getMetadata(?string $key = null)
    {
        return null;
    }
}
