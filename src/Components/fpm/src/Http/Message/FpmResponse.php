<?php

declare(strict_types=1);

namespace Imi\Fpm\Http\Message;

use Imi\Server\Http\Message\Response;
use Imi\Util\Http\Consts\MediaType;
use Imi\Util\Stream\FileStream;
use Imi\Util\Stream\StreamMode;

class FpmResponse extends Response
{
    /**
     * 被更改过的 Cookie 名称数组.
     */
    protected array $changedCookieNames = [];

    /**
     * {@inheritDoc}
     */
    public function isHeaderWritable(): bool
    {
        return !connection_aborted() && !headers_sent();
    }

    /**
     * {@inheritDoc}
     */
    public function isBodyWritable(): bool
    {
        return !connection_aborted();
    }

    /**
     * {@inheritDoc}
     */
    public function withCookie(string $key, string $value, int $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = false): self
    {
        /** @var self $self */
        $self = parent::withCookie($key, $value, $expire, $path, $domain, $secure, $httponly);
        $self->changedCookieNames[$key] = true;

        return $self;
    }

    /**
     * {@inheritDoc}
     */
    public function setCookie(string $key, string $value, int $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = false): self
    {
        parent::setCookie($key, $value, $expire, $path, $domain, $secure, $httponly);
        $this->changedCookieNames[$key] = true;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    private function sendHeaders(): void
    {
        if (!$this->isHeaderWritable())
        {
            return;
        }
        // status
        $data = $this->getStatusCode() . ' ' . $this->getReasonPhrase();
        header('HTTP/1.1 ' . $data);
        // 保证FastCGI模式下正常
        header('Status:' . $data);
        // header
        $headers = $this->getHeaders();
        if ($headers)
        {
            foreach ($headers as $name => $_)
            {
                header($name . ':' . $this->getHeaderLine($name));
            }
        }
        // cookie
        if ($this->changedCookieNames)
        {
            foreach ($this->changedCookieNames as $name => $_)
            {
                $cookie = $this->getCookie($name);
                setcookie($cookie['key'], $cookie['value'], $cookie['expire'] ?? 0, $cookie['path'] ?? '/', $cookie['domain'] ?? '', $cookie['secure'] ?? false, $cookie['httponly'] ?? false);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function send(): self
    {
        $this->sendHeaders();
        if ($this->isBodyWritable())
        {
            echo (string) $this->getBody();
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function sendFile(string $filename, ?string $contentType = null, ?string $outputFileName = null, int $offset = 0, int $length = 0): self
    {
        if (null === $outputFileName)
        {
            $outputFileName = basename($filename);
        }
        $this->setHeader('Content-Disposition', 'attachment; filename*=UTF-8\'\'' . rawurlencode($outputFileName));

        if (null === $contentType)
        {
            $outputFileNameExt = pathinfo($outputFileName, \PATHINFO_EXTENSION);
            $contentType = MediaType::getContentType($outputFileNameExt);
            if (MediaType::APPLICATION_OCTET_STREAM === $contentType)
            {
                $fileNameExt = pathinfo($filename, \PATHINFO_EXTENSION);
                if ($fileNameExt !== $outputFileNameExt)
                {
                    $contentType = MediaType::getContentType($fileNameExt);
                }
            }
        }
        $this->setHeader('Content-Type', $contentType);

        $this->sendHeaders();
        if ($this->isBodyWritable())
        {
            $fs = new FileStream($filename, StreamMode::READONLY);
            if ($offset > 0)
            {
                $fs->seek($offset);
            }
            if ($length > 0)
            {
                echo $fs->read($length);
            }
            else
            {
                echo $fs->getContents();
            }
        }

        return $this;
    }
}
