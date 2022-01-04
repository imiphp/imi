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
        $cookieParams = $this->getCookieParams();
        if ($cookieParams)
        {
            foreach ($cookieParams as $name => $cookie)
            {
                setcookie($cookie['key'], $cookie['value'], ['expires' => $cookie['expire'] ?? 0, 'path' => $cookie['path'] ?? '/', 'domain' => $cookie['domain'] ?? '', 'secure' => $cookie['secure'] ?? false, 'httponly' => $cookie['httponly'] ?? false]);
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
