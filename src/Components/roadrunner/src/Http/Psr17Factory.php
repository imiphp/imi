<?php

declare(strict_types=1);

namespace Imi\RoadRunner\Http;

use Imi\RoadRunner\Http\Message\RoadRunnerRequest;
use Imi\RoadRunner\Http\Message\RoadRunnerResponse;
use Imi\Server\Http\Message\UploadedFile;
use Imi\Util\Stream\FileStream;
use Imi\Util\Stream\MemoryStream;
use Imi\Util\Uri;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

class Psr17Factory implements RequestFactoryInterface, ResponseFactoryInterface, ServerRequestFactoryInterface, StreamFactoryInterface, UploadedFileFactoryInterface, UriFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createRequest(string $method, $uri): RequestInterface
    {
        if (\is_string($uri))
        {
            $uri = new Uri($uri);
        }

        return (new RoadRunnerRequest())->setMethod($method)->setUri($uri);
    }

    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return (new RoadRunnerResponse())->setStatus($code, $reasonPhrase);
    }

    public function createStream(string $content = ''): StreamInterface
    {
        return new MemoryStream($content);
    }

    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        return new FileStream($filename, $mode);
    }

    public function createStreamFromResource($resource): StreamInterface
    {
        return new FileStream($resource);
    }

    public function createUploadedFile(StreamInterface $stream, int $size = null, int $error = \UPLOAD_ERR_OK, string $clientFilename = null, string $clientMediaType = null): UploadedFileInterface
    {
        if (null === $size)
        {
            $size = $stream->getSize();
        }

        return new UploadedFile($clientFilename, $clientMediaType, $stream, $size, $error);
    }

    public function createUri(string $uri = ''): UriInterface
    {
        return new Uri($uri);
    }

    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        if (\is_string($uri))
        {
            $uri = new Uri($uri);
        }

        return (new RoadRunnerRequest($serverParams))->setMethod($method)->setUri($uri);
    }
}
